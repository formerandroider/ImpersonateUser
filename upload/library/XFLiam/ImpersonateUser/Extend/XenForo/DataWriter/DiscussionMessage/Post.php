<?php

class XFLiam_ImpersonateUser_Extend_XenForo_DataWriter_DiscussionMessage_Post extends XFCP_XFLiam_ImpersonateUser_Extend_XenForo_DataWriter_DiscussionMessage_Post
{
	protected function _preSaveDefaults()
	{
		if (!$this->isDiscussionFirstMessage() && XenForo_Application::isRegistered('xfliam_impersonateUser_user') && is_array($impersonateData = XenForo_Application::get('xfliam_impersonateUser_user')))
		{
			list($username, $userId) = $impersonateData;
			$this->set('username', $username);
			$this->set('user_id', $userId);

			$this->setOption(self::OPTION_SET_IP_ADDRESS, false);

			$this->setExtraData('xfliam_impersonateUser_impersonated', true);

			XenForo_Application::getInstance()->offsetUnset('xfliam_impersonateUser_user');
		}

		parent::_preSaveDefaults();
	}

	protected function _postSaveAfterTransaction()
	{
		parent::_postSaveAfterTransaction();

		if ($this->getExtraData('xfliam_impersonateUser_impersonated'))
		{
			XenForo_Model_Log::logModeratorAction('post', $this->getMergedData(), 'impersonation');
		}
	}
}

if (false)
{
	class XFCP_XFLiam_ImpersonateUser_Extend_XenForo_DataWriter_DiscussionMessage_Post extends XenForo_DataWriter_DiscussionMessage_Post
	{
	}
}