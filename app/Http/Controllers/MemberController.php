<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberController extends Controller
{
    /** Resolve auth user's linked member (for Member role) */
    private function getAuthMember(): ?Member
    {
        return auth()->user()->getLinkedMember();
    }

    /** True if logged-in user is ONLY a member (not admin/treasurer) */
    private function isMemberOnly(): bool
    {
        return auth()->user()->isMemberRole();
    }

    public function index(Request $request)
    {
        // Member role: redirect to their own profile
        if ($this->isMemberOnly()) {
            $member = $this->getAuthMember();
            if ($member) return redirect()->route('members.show', $member);
            return redirect()->route('dashboard')
                             ->with('error', 'আপনার প্রোফাইল এখনো লিঙ্ক করা হয়নি। Admin এর সাথে যোগাযোগ করুন।');
        }

        $query = Member::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('member_id', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('father_name', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('gender')) $query->where('gender', $request->gender);

        $members          = $query->latest()->paginate(20)->withQueryString();
        $totalMembers     = Member::count();
        $activeMembers    = Member::where('status','active')->count();
        $suspendedMembers = Member::where('status','suspended')->count();

        return view('members.index', compact('members','totalMembers','activeMembers','suspendedMembers'));
    }

    public function create()
    {
        if ($this->isMemberOnly()) abort(403);
        $existingMembers = Member::where('status','active')->orderBy('name')->get();
        $maxShares  = (int) Setting::get('max_shares', 2);
        $shareValue = (int) Setting::get('share_value', 1000);
        $entryFee   = (float) Setting::get('entry_fee', 100);
        return view('members.create', compact('existingMembers','maxShares','shareValue','entryFee'));
    }

    public function store(Request $request)
    {
        if ($this->isMemberOnly()) abort(403);
        $maxShares = (int) Setting::get('max_shares', 2);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'father_name'      => 'nullable|string|max:255',
            'mother_name'      => 'nullable|string|max:255',
            'spouse_name'      => 'nullable|string|max:255',
            'date_of_birth'    => 'nullable|date',
            'gender'           => 'required|in:male,female,other',
            'marital_status'   => 'required|in:married,unmarried,divorced,widowed',
            'nid_or_birth_cert'=> 'nullable|string|max:100',
            'photo'            => 'nullable|image|max:2048',
            'present_village'  => 'nullable|string|max:255',
            'present_post_office'=>'nullable|string|max:255',
            'present_union'    => 'nullable|string|max:255',
            'present_ward'     => 'nullable|string|max:20',
            'present_upazila'  => 'nullable|string|max:255',
            'present_district' => 'nullable|string|max:255',
            'permanent_village'  => 'nullable|string|max:255',
            'permanent_post_office'=>'nullable|string|max:255',
            'permanent_union'    => 'nullable|string|max:255',
            'permanent_ward'     => 'nullable|string|max:20',
            'permanent_upazila'  => 'nullable|string|max:255',
            'permanent_district' => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'nominee_name'     => 'nullable|string|max:255',
            'nominee_father_spouse' => 'nullable|string|max:255',
            'nominee_relation' => 'nullable|string|max:100',
            'nominee_phone'    => 'nullable|string|max:20',
            'nominee_nid_or_birth_cert'=>'nullable|string|max:100',
            'join_date'        => 'required|date',
            'entry_fee'        => 'required|numeric|min:0',
            'share_count'      => "required|integer|min:1|max:{$maxShares}",
            'monthly_deposit'  => 'required|numeric|min:0',
            'referred_by_member_id' => 'nullable|exists:members,id',
            'status'           => 'required|in:active,inactive,suspended',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members/photos','public');
        }
        $data['member_id'] = Member::generateMemberId();
        $member = Member::create($data);
        ActivityLog::log('create', "সদস্য যোগ: {$member->name} ({$member->member_id})", $member);

        return redirect()->route('members.show', $member)
                         ->with('success', "সদস্য {$member->name} সফলভাবে যোগ হয়েছে।");
    }

    public function show(Member $member)
    {
        // Member role: only allow viewing own profile
        if ($this->isMemberOnly()) {
            $authMember = $this->getAuthMember();
            if (!$authMember || $authMember->id !== $member->id) {
                abort(403, 'আপনি শুধুমাত্র আপনার নিজের প্রোফাইল দেখতে পারবেন।');
            }
        }

        $bills    = $member->bills()->latest()->paginate(12);
        $payments = $member->payments()->with('bill')->latest()->paginate(10);
        return view('members.show', compact('member','bills','payments'));
    }

    public function edit(Member $member)
    {
        if ($this->isMemberOnly()) abort(403);
        $existingMembers = Member::where('status','active')->where('id','!=',$member->id)->orderBy('name')->get();
        $maxShares  = (int) Setting::get('max_shares', 2);
        $shareValue = (int) Setting::get('share_value', 1000);
        return view('members.edit', compact('member','existingMembers','maxShares','shareValue'));
    }

    public function update(Request $request, Member $member)
    {
        if ($this->isMemberOnly()) abort(403);
        $maxShares = (int) Setting::get('max_shares', 2);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'father_name'      => 'nullable|string|max:255',
            'mother_name'      => 'nullable|string|max:255',
            'spouse_name'      => 'nullable|string|max:255',
            'date_of_birth'    => 'nullable|date',
            'gender'           => 'required|in:male,female,other',
            'marital_status'   => 'required|in:married,unmarried,divorced,widowed',
            'nid_or_birth_cert'=> 'nullable|string|max:100',
            'photo'            => 'nullable|image|max:2048',
            'present_village'  => 'nullable|string|max:255',
            'present_post_office'=>'nullable|string|max:255',
            'present_union'    => 'nullable|string|max:255',
            'present_ward'     => 'nullable|string|max:20',
            'present_upazila'  => 'nullable|string|max:255',
            'present_district' => 'nullable|string|max:255',
            'permanent_village'  => 'nullable|string|max:255',
            'permanent_post_office'=>'nullable|string|max:255',
            'permanent_union'    => 'nullable|string|max:255',
            'permanent_ward'     => 'nullable|string|max:20',
            'permanent_upazila'  => 'nullable|string|max:255',
            'permanent_district' => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'nominee_name'     => 'nullable|string|max:255',
            'nominee_father_spouse' => 'nullable|string|max:255',
            'nominee_relation' => 'nullable|string|max:100',
            'nominee_phone'    => 'nullable|string|max:20',
            'nominee_nid_or_birth_cert'=>'nullable|string|max:100',
            'join_date'        => 'required|date',
            'entry_fee'        => 'required|numeric|min:0',
            'share_count'      => "required|integer|min:1|max:{$maxShares}",
            'monthly_deposit'  => 'required|numeric|min:0',
            'referred_by_member_id' => 'nullable|exists:members,id',
            'status'           => 'required|in:active,inactive,suspended',
        ]);

        if ($request->hasFile('photo')) {
            if ($member->photo) Storage::disk('public')->delete($member->photo);
            $data['photo'] = $request->file('photo')->store('members/photos','public');
        }
        $member->update($data);
        ActivityLog::log('update', "সদস্য আপডেট: {$member->name}", $member);

        return redirect()->route('members.show', $member)->with('success', 'সদস্য আপডেট হয়েছে।');
    }

    public function destroy(Member $member)
    {
        if ($this->isMemberOnly()) abort(403);
        if ($member->photo) Storage::disk('public')->delete($member->photo);
        ActivityLog::log('delete', "সদস্য মুছে ফেলা: {$member->name}", $member);
        $member->delete();
        return redirect()->route('members.index')->with('success', 'সদস্য মুছে ফেলা হয়েছে।');
    }

    public function registrationPdf(Member $member)
    {
        // Member can only print their own PDF
        if ($this->isMemberOnly()) {
            $authMember = $this->getAuthMember();
            if (!$authMember || $authMember->id !== $member->id) abort(403);
        }
        $pdf = Pdf::loadView('members.registration_pdf', compact('member'))
                  ->setPaper([0,0,595,842], 'portrait');
        return $pdf->download("member-form-{$member->member_id}.pdf");
    }
}
