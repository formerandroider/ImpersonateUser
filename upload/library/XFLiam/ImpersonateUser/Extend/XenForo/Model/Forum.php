<?php

class XFLiam_ImpersonateUser_Extend_XenForo_Model_Forum extends XFCP_XFLiam_ImpersonateUser_Extend_XenForo_Model_Forum
{
	public function canImpersonateUser(array $forum, $user = null, &$errorPhraseKey = '', array $nodePermissions = null, array $viewingUser = null)
	{
		$this->standardizeViewingUserReferenceForNode($forum['node_id'], $viewingUser, $nodePermissions);

		if (is_array($user) && $user['user_id'])
		{
			if (!isset($user['node_permission_cache']))
			{
				/** @var XenForo_Model_PermissionCache $permsModel */
				$permsModel = $this->getModelFromCache('XenForo_Model_PermissionCache');
				$user['node_permission_cache'] = $permsModel->getContentPermissionsForItem($user['permission_combination_id'], 'node', $forum['node_id']);
			}

			$userNodePermissions = XenForo_Permission::unserializePermissions($user['node_permission_cache']);

			if (XenForo_Permission::hasContentPermission($userNodePermissions, 'xfliam_noImpersonate'))
			{
				$errorPhraseKey = array(
					'xfliam_impersonateUser_x_cannot_be_impersonated_in_this_forum',
					'name' => $user['username']
				);

				return false;
			}
		}

		return XenForo_Permission::hasContentPermission($nodePermissions, 'xfliam_impersonateUser');
	}
}

if (false)
{
	class XFCP_XFLiam_ImpersonateUser_Extend_XenForo_Model_Forum extends XenForo_Model_Forum
	{
	}
}