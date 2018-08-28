<?php


if (!isset($params['parent-module']) and isset($params['root-module'])) {
    $params['parent-module'] = $params['root-module'];
}
if (!isset($params['parent-module-id']) and isset($params['root-module-id'])) {
    $params['parent-module-id'] = $params['root-module-id'];
}

if (!isset($params['parent-module']) and isset($params['prev-module'])) {
    $params['parent-module'] = $params['prev-module'];
}
if (!isset($params['parent-module-id']) and isset($params['prev-module-id'])) {
    $params['parent-module-id'] = $params['prev-module-id'];
}

if (isset($params['for-module'])) {
    $params['parent-module'] = $params['for-module'];
}
if (!isset($params['parent-module'])) {
    error('parent-module is required');

}

if (!isset($params['parent-module-id'])) {
    error('parent-module-id is required');

}


$site_templates = site_templates();

$module_templates = module_templates($params['parent-module']);
$templates = module_templates($params['parent-module']);

$mod_name = $params['parent-module'];
$mod_name = str_replace('admin', '', $mod_name);
$mod_name = rtrim($mod_name, DS);
$mod_name = rtrim($mod_name, '/');

$screenshots = false;
if (isset($params['data-screenshots'])) {
    $screenshots = $params['data-screenshots'];
}

$cur_template = get_option('data-template', $params['parent-module-id']);
if ($cur_template == false) {

    if (isset($_GET['data-template'])) {
        $cur_template = $_GET['data-template'] . '.php';
    } else if (isset($_REQUEST['template'])) {
        $cur_template = $_REQUEST['template'] . '.php';
    }
    if ($cur_template != false) {
        $cur_template = str_replace('..', '', $cur_template);
        $cur_template = str_replace('.php.php', '.php', $cur_template);
    }
}


?>
<?php if (is_array($templates)): ?>

    <div class="mw-mod-template-settings-holder">
        <?php $default_item_names = array(); ?>
        <label class="mw-ui-label">
            <?php _e("Current Skin / Template"); ?>
        </label>

        <select data-also-reload="#mw-module-skin-settings-module" name="data-template" class="mw-ui-field mw_option_field  w100" option_group="<?php print $params['parent-module-id'] ?>" data-refresh="<?php print $params['parent-module-id'] ?>">
            <option value="default" <?php if (('default' == $cur_template)): ?>   selected="selected"  <?php endif; ?>>
                <?php _e("Default"); ?>
            </option>

            <?php foreach ($templates as $item): ?>
                <?php if ((strtolower($item['name']) != 'default')): ?>
                    <?php $default_item_names[] = $item['name']; ?>
                    <option <?php if (($item['layout_file'] == $cur_template)): ?>   selected="selected" <?php endif; ?> value="<?php print $item['layout_file'] ?>" title="Template: <?php print str_replace('.php', '', $item['layout_file']); ?>"> <?php print $item['name'] ?> </option>
                <?php endif; ?>
            <?php endforeach; ?>


            <?php if (is_array($site_templates)): ?>
                <?php foreach ($site_templates as $site_template): ?>
                    <?php if (isset($site_template['dir_name'])): ?>
                        <?php
                        $template_dir = templates_path() . $site_template['dir_name'];
                        $possible_dir = $template_dir . DS . 'modules' . DS . $mod_name . DS;
                        $possible_dir = normalize_path($possible_dir, false)
                        ?>
                        <?php if (is_dir($possible_dir)): ?>
                            <?php
                            $options = array();

                            $options['for_modules'] = 1;
                            $options['path'] = $possible_dir;
                            $templates = mw()->layouts_manager->get_all($options);
                            ?>

                            <?php if (is_array($templates)): ?>
                                <?php if ($site_template['dir_name'] == template_name()) { ?>
                                    <?php
                                    $has_items = false;

                                    foreach ($templates as $item) {
                                        if (!in_array($item['name'], $default_item_names)) {
                                            $has_items = true;
                                        }
                                    }
                                    ?>
                                    <?php if (is_array($has_items)): ?>
                                        <optgroup label="<?php print $site_template['name']; ?>">
                                            <?php foreach ($templates as $item): ?>
                                                <?php if ((strtolower($item['name']) != 'default')): ?>
                                                    <?php $opt_val = $site_template['dir_name'] . '/' . 'modules/' . $mod_name . $item['layout_file']; ?>
                                                    <?php if (!in_array($item['name'], $default_item_names)): ?>
                                                        <option <?php if (($opt_val == $cur_template)): ?>   selected="selected"  <?php endif; ?> value="<?php print $opt_val; ?>"><?php print $item['name'] ?></option>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                <?php } ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>


        </select>

        <?php if ($screenshots): ?>
            <div class="module-layouts-viewer">
                <?php foreach ($module_templates as $item): ?>
                    <?php if ((strtolower($item['name']) != 'default')): ?>
                        <div class="screenshot <?php if (($item['layout_file'] == $cur_template)): ?>active<?php endif; ?>">
                            <?php
                            $item_screenshot = thumbnail('');
                            if (isset($item['screenshot'])) {
                                $item_screenshot = $item['screenshot'];
                            }
                            ?>

                            <div class="holder">
                                <img src="<?php echo $item_screenshot; ?>" alt="<?php print $item['name']; ?>" style="max-width:100%;" title="<?php print $item['name']; ?>"/>
                                <div class="title"><?php print $item['name']; ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>


        <module type="admin/modules/templates_settings" id="mw-module-skin-settings-module" parent-module-id="<?php print $params['parent-module-id'] ?>"
                parent-module="<?php print $params['parent-module'] ?>" parent-template="<?php print $cur_template ?>"/>
        <?php if (!isset($params['simple'])) { ?>
            <label class="mw-ui-label">
                <hr>
                <small>
                    <?php _e("Need more designs"); ?>
                    ?<br>
                    <?php _e("You can use all templates you like and change the skin"); ?>
                    .
                </small>
            </label>
            <a class="mw-ui-link" target="_blank" href="<?php print mw()->update->marketplace_admin_link($params); ?>">
                <?php _e("Browse Templates"); ?>
            </a>
        <?php } ?>
    </div>
<?php endif; ?>
