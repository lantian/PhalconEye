<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace User\Grid\Backoffice;

use Core\Grid\CoreGrid;
use Engine\Grid\GridItem;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Query\Builder;
use User\Model\RoleModel;

/**
 * User grid.
 *
 * @category  PhalconEye
 * @package   Core\Controller\Grid\Admin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class UserGrid extends CoreGrid
{
    /**
     * Get main select builder.
     *
     * @return Builder
     */
    public function getSource()
    {
        $builder = new Builder();
        $builder
            ->columns(['u.*', 'r.name'])
            ->addFrom('User\Model\UserModel', 'u')
            ->leftJoin('User\Model\RoleModel', 'u.role_id = r.id', 'r')
            ->orderBy('u.id DESC');

        return $builder;
    }

    /**
     * Get item action (Edit, Delete, etc).
     *
     * @param GridItem $item One item object.
     *
     * @return array
     */
    public function getItemActions(GridItem $item)
    {
        return [
            'Edit' => ['href' => ['for' => 'backoffice-users-edit', 'id' => $item['u.id']]],
            'Delete' => [
                'href' => ['for' => 'backoffice-users-delete', 'id' => $item['u.id']],
                'attr' => ['class' => 'grid-action-delete']
            ]
        ];
    }

    /**
     * Initialize grid columns.
     *
     * @return array
     */
    protected function _initColumns()
    {
        $this
            ->addTextColumn(
                'u.id',
                'ID',
                [
                    self::COLUMN_PARAM_TYPE => Column::BIND_PARAM_INT,
                    self::COLUMN_PARAM_OUTPUT_LOGIC =>
                        function (GridItem $item, $di) {
                            $url = $di->get('url')->get(
                                ['for' => 'backoffice-users-view', 'id' => $item['u.id']]
                            );
                            return sprintf('<a href="%s">%s</a>', $url, $item['u.id']);
                        }
                ]
            )
            ->addTextColumn('u.username', 'Username')
            ->addTextColumn('u.email', 'Email')
            ->addSelectColumn(
                'r.name',
                'Role',
                ['hasEmptyValue' => true, 'using' => ['name', 'name'], 'elementOptions' => RoleModel::find()],
                [
                    self::COLUMN_PARAM_USE_HAVING => false,
                    self::COLUMN_PARAM_USE_LIKE => false,
                    self::COLUMN_PARAM_OUTPUT_LOGIC =>
                        function (GridItem $item) {
                            return $item['name'];
                        }
                ]
            )
            ->addTextColumn('u.creation_date', 'Creation Date');
    }
}