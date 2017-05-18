<?php

namespace Modules\Core\Http\Controllers\API;

use Modules\Core\Models\Member;
use Illuminate\Routing\Controller;
use Modules\Core\Contracts\Context;

class MemberController extends Controller
{
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }


    public function postMember()
    {
        return $this->context->status(200);
    }

    /**
     *  编辑用户资料.
     */
    public function putMember($member_id)
    {
        $member = Member::find($member_id);

        if (!$member) {

            exception(1001);
        }

        $member->member_avatar = $this->context->data('member_avatar', $member->member_avatar);
        $member->member_nickname = $this->context->data('member_nickname', $member->member_nickname);

        $member->save();

        return $this->context->status(200);

    }

    public function deleteMember()
    {
        $member_id = $this->context->query('member_id');

    }

    public function getMember($member_id)
    {
        return $this->context->status(200, Member::find($member_id));
    }

    public function getMembers()
    {

    }

}
