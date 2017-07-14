<?php
/**
 * Injects location stuff into the api system.
 *
 * PHP version 5
 *
 * @category AddLocationAPI
 * @package  FOGProject
 * @author   Fernando Gietz <fernando.gietz@gmail.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Injects location stuff into the api system.
 *
 * @category AddLocationAPI
 * @package  FOGProject
 * @author   Fernando Gietz <fernando.gietz@gmail.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class AddLocationAPI extends Hook
{
    /**
     * The name of the hook.
     *
     * @var string
     */
    public $name = 'AddLocationAPI';
    /**
     * The description.
     *
     * @var string
     */
    public $description = 'Add Site stuff into the api system.';
    /**
     * For posterity.
     *
     * @var bool
     */
    public $active = true;
    /**
     * The node the hook works with.
     *
     * @var string
     */
    public $node = 'location';
    /**
     * Initialize object.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        self::$HookManager
            ->register(
                'API_VALID_CLASSES',
                array(
                    $this,
                    'injectAPIElements'
                )
            )
            ->register(
                'API_GETTER',
                array(
                    $this,
                    'adjustGetter'
                )
            )
            ->register(
                'API_INDIVDATA_MAPPING',
                array(
                    $this,
                    'adjustIndivInfoUpdate'
                )
            )
            ->register(
                'API_MASSDATA_MAPPING',
                array(
                    $this,
                    'adjustMassInfo'
                )
            );
    }
    /**
     * This function injects site elements for
     * api access.
     *
     * @param mixed $arguments The arguments to modify.
     *
     * @return void
     */
    public function injectAPIElements($arguments)
    {
        if (!in_array($this->node, (array)self::$pluginsinstalled)) {
            return;
        }
        $arguments['validClasses'] = self::fastmerge(
            $arguments['validClasses'],
            array(
                'location',
                'locationassociation'
            )
        );
    }
    /**
     * This function changes the api data map as needed.
     *
     * @param mixed $arguments The arguments to modify.
     *
     * @return void
     */
    public function adjustIndivInfoUpdate($arguments)
    {
        if (!in_array($this->node, (array)self::$pluginsinstalled)) {
            return;
        }
    }
    /**
     * This function changes the api data map as needed.
     *
     * @param mixed $arguments The arguments to modify.
     *
     * @return void
     */
    public function adjustMassInfo($arguments)
    {
        if (!in_array($this->node, (array)self::$pluginsinstalled)) {
            return;
        }
        $find = Route::getsearchbody($arguments['classname']);
        switch ($arguments['classname']) {
        case 'location':
            $arguments['data'][$arguments['classname'].'s'] = array();
            $arguments['data']['count'] = 0;
            foreach ((array)$arguments['classman']->find($find) as &$location) {
                $arguments['data'][$arguments['classname'].'s'][] = $location->get();
                $arguments['data']['count']++;
                unset($location);
            }
            break;
        case 'locationassociation':
            $arguments['data'][$arguments['classname'].'s'] = array();
            $arguments['data']['count'] = 0;
            foreach ((array)$arguments['classman']->find($find) as &$locationassoc) {
                $arguments['data'][$arguments['classname'].'s'][]
                    = $locationassoc->get();
                $arguments['data']['count']++;
                unset($locationassoc);
            }
            break;
        }
    }
    /**
     * This function changes the getter to enact on this particular item.
     *
     * @param mixed $arguments The arguments to modify.
     *
     * @return void
     */
    public function adjustGetter($arguments)
    {
        if (!in_array($this->node, (array)self::$pluginsinstalled)) {
            return;
        }
        switch ($arguments['classname']) {
        case 'location':
            $arguments['data'] = FOGCore::fastmerge(
                $arguments['class']->get(),
                array(
                    'hosts' => $arguments['class']->get('hosts'),
                    'storagenode' => $arguments['class']->get('storagenode')->get(),
                    'storagegroup' => $arguments['class']
                    ->get('storagegroup')
                    ->get()
                )
            );
            break;
        case 'locationassociation':
            $arguments['data'] = FOGCore::fastmerge(
                $arguments['class']->get(),
                array(
                    'host' => $arguments['class']->get('host')->get(),
                    'location' => $arguments['class']->get('location')->get()
                )
            );
            break;
        }
    }
}