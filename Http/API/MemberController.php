<?php

namespace Modules\Auth\Http\API;

use Gindowin\Request;
use Modules\Auth\Events\MemberUpdateEvent;
use Modules\Auth\Models\Member;
use Illuminate\Routing\Controller;

class MemberController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function postMember()
    {
        return status(200);
    }

    /**
     *  编辑用户资料.
     */
    public function putMember()
    {
        $member_id = $this->request->input('member_id');

        $member = Member::find($member_id);

        if (!$member) {

            exception(1001);
        }

        $member->member_avatar = $this->request->input('member_avatar', $member->member_avatar);
        $member->member_nickname = $this->request->input('member_nickname', $member->member_nickname);

        $member->save();

        event(new MemberUpdateEvent($member, collect($this->request->input())));

        return status(200);

    }

    public function deleteMember()
    {
        $member_id = $this->request->query('member_id');

    }

    public function getMember($member_id)
    {
        return status(200, Member::find($member_id));
    }

    public function getMembers()
    {

    }

}
