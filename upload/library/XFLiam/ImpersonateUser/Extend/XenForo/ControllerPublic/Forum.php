<?php

class XFLiam_ImpersonateUser_Extend_XenForo_ControllerPublic_Forum extends XFCP_XFLiam_ImpersonateUser_Extend_XenForo_ControllerPublic_Forum
{
	public function actionCreateThread()
	{
		$response = parent::actionCreateThread();

		if ($response instanceof XenForo_ControllerResponse_View)
		{
			$response->params['canImpersonateUser'] = $this->_getForumModel()->canImpersonateUser($response->params['forum']);
		}

		return $response;
	}

	public function actionAddThread()
	{
		$this->_assertPostOnly();

		$forumId = $this->_input->filterSingle('node_id', XenForo_Input::UINT);
		$forumName = $this->_input->filterSingle('node_name', XenForo_Input::STRING);

		$ftpHelper = $this->getHelper('ForumThreadPost');
		$forum = $ftpHelper->assertForumValidAndViewable($forumId ? $forumId : $forumName);

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

		return parent::actionAddThread();
	}
}

if (false)
{
	class XFCP_XFLiam_ImpersonateUser_Extend_XenForo_ControllerPublic_Forum extends XenForo_ControllerPublic_Forum
	{
	}
}