<style>
    .form-table { font-size:12px; }
    .form-table tbody:nth-child(odd) { background:#eee; }
</style>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>PhotoSwiper Settings</h2>

    <?php if (isset($_POST['message'])) { ?>
        <div class="updated below-h2">
            <p><?php echo($_POST['message']); ?></p>
        </div>
    <?php } ?>

    <form id="photoswiper-form" method="post" action="options.php" ng-submit="submit()" ng-controller="formCtrl" ng-cloak>
        <?php settings_fields('photoswiper_options'); ?>

        <!--
            Wordpress's "register_settings" stuff can't save an array so I keep a JSON obj of the form's data
            that ends up being the only value that WP saves.
        -->
        <input type="text" name="photoswiper" ng-model="photoswiper" form-watch="photoswiper" style="display:none;"/>

        <table class="form-table">
            <tbody ng-repeat="field in schema" ng-switch on="field.type">
                <tr valign="top" ng-switch-when="bool">
                    <th scope="row">
                        <label>{{ field.label }}</label>
                    </th>
                    <td>
                        <label>
                            <input name="{{ field.name }}" type="checkbox" data-ng-model="field.value"/>
                            <span class="description"> &nbsp; {{ field.help }}</span>
                        </label>                        
                    </td>
                </tr>

                <tr valign="top" ng-switch-when="int">
                    <th scope="row">
                        <label for="{{ field.name }}">{{ field.label }}</label>
                    </th>
                    <td>
                        <input name="{{ field.name }}" type="text" data-ng-model="field.value" class="regular-text"/>
                        <p class="description">{{ field.help }}</p>
                    </td>
                </tr>

                <tr valign="top" ng-switch-when="float">
                    <th scope="row">
                        <label for="{{ field.name }}">{{ field.label }}</label>
                    </th>
                    <td>
                        <input name="{{ field.name }}" type="text" data-ng-model="field.value" class="regular-text"/>
                        <p class="description">{{ field.help }}</p>
                    </td>
                </tr>

                <tr valign="top" ng-switch-when="string">
                    <th scope="row">
                        <label for="{{ field.name }}">{{ field.label }}</label>
                    </th>
                    <td>
                        <input name="{{ field.name }}" type="text" data-ng-model="field.value" class="regular-text"/>
                        <p class="description">{{ field.help }}</p>
                    </td>
                </tr>

                <tr valign="top" ng-switch-when="select">
                    <th scope="row">
                        <label for="{{ field.name }}">{{ field.label }}</label>
                    </th>
                    <td>
                        <select data-ng-model="field.value" ng-options="option for option in field.options"></select>
                        <p class="description">{{ field.help }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input class="button-primary" type="submit" name="photoswiper_save" value="<?php _e('Save Options'); ?>" id="submitbutton" />
        </p>
    </form>
</div>