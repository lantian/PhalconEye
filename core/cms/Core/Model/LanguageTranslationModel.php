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

namespace Core\Model;

use Engine\Db\AbstractModel;
use Engine\Translation\TranslationModelInterface;
use Phalcon\Di;

/**
 * Language translation.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("language_translations")
 * @BelongsTo("language_id", "\Core\Model\LanguageModel", "id", {
 *  "alias": "LanguageModel"
 * })
 */
class LanguageTranslationModel extends AbstractModel implements TranslationModelInterface
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=false, column="language_id", size="11")
     */
    public $language_id;

    /**
     * @Column(type="string", nullable=true, column="scope", size="25")
     */
    public $scope = null;

    /**
     * @Column(type="text", nullable=false, column="original")
     */
    public $original;

    /**
     * @Column(type="text", nullable=false, column="translated")
     */
    public $translated = null;

    /**
     * @Column(type="boolean", nullable=false, column="checked")
     */
    public $checked = false;

    /**
     * Set scope.
     *
     * @param string $scope Scope name.
     *
     * @return mixed
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Set language id.
     *
     * @param int $languageId Language id.
     *
     * @return mixed
     */
    public function setLanguageId($languageId)
    {
        $this->language_id = $languageId;
    }

    /**
     * Set translation original text.
     *
     * @param string $text Original text.
     *
     * @return mixed
     */
    public function setOriginal($text)
    {
        $this->original = $text;
    }

    /**
     * Set translated text.
     *
     * @param string $text Translated text.
     *
     * @return mixed
     */
    public function setTranslated($text)
    {
        $this->translated = $text;
    }

    /**
     * Get translated data.
     *
     * @return string
     */
    public function getTranslated()
    {
        return $this->translated;
    }

    /**
     * Return the related "Language" entity.
     *
     * @param array $arguments Entity params.
     *
     * @return LanguageModel
     */
    public function getLanguage($arguments = [])
    {
        return $this->getRelated('LanguageModel', $arguments);
    }

    /**
     * Copy translations from one language to another.
     *
     * @param $id Destination language.
     * @param $defaultLanguageId Source language.
     *
     * @return mixed Rows.
     */
    public static function copyTranslations($id, $defaultLanguageId)
    {
        $di = Di::getDefault();
        $table = self::getTableName();

        return $di->getDb()->query(
            "
            INSERT INTO `{$table}` (language_id, original, translated, scope, checked)
            SELECT {$id}, original, translated, scope, checked FROM `{$table}`
            WHERE language_id = {$defaultLanguageId} AND original NOT IN
              (SELECT original FROM `{$table}` WHERE language_id = {$id});
            "
        );
    }
}