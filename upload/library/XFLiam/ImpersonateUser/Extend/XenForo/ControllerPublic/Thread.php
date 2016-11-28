<?php

class XFLiam_ImpersonateUser_Extend_XenForo_ControllerPublic_Thread extends XFCP_XFLiam_ImpersonateUser_Extend_XenForo_ControllerPublic_Thread
{
	public function actionAddReply()
	{
		$this->_assertPostOnly();

		if ($this->_input->inRequest('more_options'))
		{
			return $this->responseReroute(__CLASS__, 'reply');
		}

		$threadId = $this->_input->filterSingle('thread_id', XenForo_Input::UINT);

		$visitor = XenForo_Visitor::getInstance();

		$ftpHelper = $this->getHelper('ForumThreadPost');
		$threadFetchOptions = array('readUserId' => $visitor['user_id']);
		$forumFetchOptions = array('readUserId' => $visitor['user_id']);
		list($thread, $forum) = $ftpHelper->assertThreadValidAndViewable($threadId, $threadFetchOptions, $forumFetchOptions);

		$impersonateUsername = $this->_input->filterSingle('impersonate_username', XenForo_Input::STRING);
		$impersonateUser = $this->getModelFromCache('XenForo_Model_User')->getUserByName($impersonateUsername, array('nodeIdPermissions' => $forum['node_id']));

		if ($impersonateUsername && $this->_getForumModel()->canImpersonateUser($forum, $impersonateUser, $errorPhraseKey))
		{
			if (!$impersonateUser)
			{
				return $this->responseError(new XenForo_Phrase('requested_user_not_found'), 404);
			}

			XenForo_Application::set('xfliam_impersonateUser_user', $impersonateUser);
		}
		else if ($impersonateUser && $errorPhraseKey)
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}

		return parent::actionAddReply();
	}

	protected function _getDefaultViewParams(array $forum, array $thread, array $posts, $page = 1, array $viewParams = array())
	{
		$viewParams = parent::_getDefaultViewParams($forum, $thread, $posts, $page, $viewParams);

		$viewParams['canImpersonateUser'] = $this->_getForumModel()->canImpersonateUser($forum);

		return $viewParams;
	}
}

if (false)
{
	class XFCP_XFLiam_ImpersonateUser_Extend_XenForo_ControllerPublic_Thread extends XenForo_ControllerPublic_Thread
	{
	}
}