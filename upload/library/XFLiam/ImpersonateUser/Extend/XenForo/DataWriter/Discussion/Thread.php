<?php

class XFLiam_ImpersonateUser_Extend_XenForo_DataWriter_Discussion_Thread extends XFCP_XFLiam_ImpersonateUser_Extend_XenForo_DataWriter_Discussion_Thread
{
	protected function _preSaveDefaults()
	{
		if (XenForo_Application::isRegistered('xfliam_impersonateUser_user') && is_array($impersonateAs = XenForo_Application::get('xfliam_impersonateUser_user')))
		{
			$this->set('username', $impersonateAs['username']);
			$this->set('user_id', $impersonateAs['user_id']);

			$this->getFirstMessageDw()->setOption(XenForo_DataWriter_DiscussionMessage_Post::OPTION_SET_IP_ADDRESS, false);
		}

		parent::_preSaveDefaults();
	}

	protected function _postSaveAfterTransaction()
	{
		parent::_postSaveAfterTransaction();

		if (XenForo_Application::isRegistered('xfliam_impersonateUser_user') && is_array($impersonateAs = XenForo_Application::get('xfliam_impersonateUser_user')))
		{
			XenForo_Model_Log::logModeratorAction('thread', $this->getMergedData(), 'impersonation');
		}
	}
}

if (false)
{
	class XFCP_XFLiam_ImpersonateUser_Extend_XenForo_DataWriter_Discussion_Thread extends XenForo_DataWriter_Discussion_Thread
	{
	}
}