<?php

namespace Nikeli;

class SettingsPage {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Gestión de Configuración de nikeli',  // Título de la página actualizado
            'Configuración Nikeli',             // Título del menú actualizado
            'manage_options', 
            'discount_management', 
            [$this, 'settings_page_html']
        );
    }

       public function enqueue_media_uploader($hook_suffix) {
        // Solo carga estos scripts en la página de ajustes de tu plugin
        if ('toplevel_page_discount_management' === $hook_suffix) {
            wp_enqueue_media();
        }
    }

    public function settings_page_html() { 
        ?>
        <form action="options.php" method="post">
            <h1>Gestión de Descuentos y Conversión de Puntos</h1>
            <?php
            settings_fields('discountSettings');
            do_settings_sections('discount_management');
            submit_button('Guardar Cambios');
            ?>
        </form>
        <?php
    }

    public function settings_init() { 
        register_setting('discountSettings', 'discounts_settings');
        register_setting('discountSettings', 'gallery_images');
        register_setting('discountSettings', 'custom_link');

        // Rangos
        register_setting('discountSettings', 'rango_master');
        register_setting('discountSettings', 'rango_bronce');
        register_setting('discountSettings', 'rango_bronce_elite');
        register_setting('discountSettings', 'rango_plata');
        register_setting('discountSettings', 'rango_plata_elite');
        register_setting('discountSettings', 'rango_oro');
        register_setting('discountSettings', 'rango_oro_elite');
        register_setting('discountSettings', 'rango_diamante');
        register_setting('discountSettings', 'rango_socio');

        // Material apoyo
        // Descuentos por rol
        add_settings_section(
            'nikeli_discounts_section', 
            __('Descuentos por rol de usuario', 'nikeli'), 
            [$this, 'settings_section_callback'], 
            'discount_management'
        );

        // URL del Enlace de Catálogo	
        // Master
        add_settings_section(
            'nikeli_custom_ranges_section',
            'Rangos alcanzados',
            [$this, 'rango_master_callback'],
            'discount_management'
        );


  
      


        add_settings_section(
            'nikeli_custom_link_section',
            'Configuración Adicional',
            null,
            'discount_management'
        );

        // URL DE GALERIA DE IMAGENES 
        add_settings_field(
            'gallery_images',
            'Seleccione Imágenes para la Galería',
            [$this, 'gallery_images_callback'],
            'discount_management',
            'nikeli_conversion_section' 
        );



        // Material de apoyo


        add_settings_section(
            'nikeli_material_apoyo_extended_section', // ID de la sección
            'Material de Apoyo Extendido',            // Título de la sección
            null,                                     // Callback de la sección, no necesario
            'discount_management'                     // Slug de la página donde se mostrará la sección
        );
    
        for ($i = 1; $i <= 3; $i++) {
            // Campo para el título
            add_settings_field(
                'material_apoyo_titulo_' . $i,
                'Material de Apoyo ' . $i . ' Título',
                [$this, 'material_apoyo_titulo_callback'],
                'discount_management',
                'nikeli_material_apoyo_extended_section',
                ['label_for' => 'material_apoyo_titulo_' . $i]
            );
    
            // Campo para la URL
            add_settings_field(
                'material_apoyo_url_' . $i,
                'Material de Apoyo ' . $i . ' URL',
                [$this, 'material_apoyo_url_callback'],
                'discount_management',
                'nikeli_material_apoyo_extended_section',
                ['label_for' => 'material_apoyo_url_' . $i]
            );
    
            // Registro de las opciones para cada par de campos
            register_setting('discountSettings', 'material_apoyo_titulo_' . $i);
            register_setting('discountSettings', 'material_apoyo_url_' . $i);
        }


        // CATALOGO
        add_settings_field(
            'custom_link',
            'URL del Enlace de Catálogo',
            [$this, 'custom_link_callback'],
            'discount_management',
            'nikeli_custom_link_section',
        );

        $roles = ['ejecutivo', 'plus', 'top'];
        foreach ($roles as $role) {
            add_settings_field(
                'discount_rate_' . $role, 
                __('Descuento para ' . ucfirst($role), 'nikeli'), 
                [$this, 'settings_field_callback'], 
                'discount_management', 
                'nikeli_discounts_section',
                [
                    'label_for' => 'discount_rate_' . $role,
                    'class' => 'nikeli_row',
                    'nikeli_custom_data' => 'custom',
                    'role' => $role
                ]
            );
        }

        // Configuración de conversión de puntos
        add_settings_section(
            'nikeli_conversion_section',
            'Configuración de Conversión de Puntos',
            null,
            'discount_management'
        );

        add_settings_field(
            'points_threshold', 
            'Número de Puntos', 
            [$this, 'points_threshold_callback'], 
            'discount_management', 
            'nikeli_conversion_section'
        );

        add_settings_field(
            'soles_per_point',
            'UNIDAD DE MEDIDA CENTIMOS. 1 SOL EQUIVALE A  1.0 CENTIMOS',
            [$this, 'soles_per_point_callback'],
            'discount_management',
            'nikeli_conversion_section'
        );

        register_setting('discountSettings', 'points_threshold');
        register_setting('discountSettings', 'soles_per_point');
    }

    public function settings_section_callback() { 
        echo __('Ajuste los porcentajes de descuento y las tasas de conversión aplicables.', 'nikeli');
    }

    public function settings_field_callback($args) { 
        $options = get_option('discounts_settings');
        ?>
        <input type="number" name="discounts_settings[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo $options[$args['label_for']] ?? ''; ?>" min="0" max="100" step="0.01"> %
        <?php
    }

    public function points_threshold_callback() {
        $points_threshold = get_option('points_threshold','10');
        ?>
        <input type="number" id="points_threshold" name="points_threshold" value="<?php echo esc_attr($points_threshold); ?>" min="10" step="10" />
        <?php
    }

    public function soles_per_point_callback() {
        $soles_per_point = get_option('soles_per_point', '0.10');
        ?>
        <input type="number" id="soles_per_point" name="soles_per_point" value="<?php echo esc_attr($soles_per_point); ?>" min="0.10"  step="0.10" />
        <?php
    }


    // Custom Link
    public function custom_link_callback() {
        $custom_link = get_option('custom_link', '');
        ?>
        <input type="text" id="custom_link" name="custom_link" value="<?php echo esc_attr($custom_link); ?>" style="width: 50%;" />
        <?php
    }

    // Material de apoyo
    public function material_apoyo_titulo_callback($args) {
        $option = get_option($args['label_for']);
        ?>
        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($args['label_for']); ?>" value="<?php echo esc_attr($option); ?>" style="width: 50%;" />
        <?php
    }
    
    public function material_apoyo_url_callback($args) {
        $option = get_option($args['label_for']);
        ?>
        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($args['label_for']); ?>" value="<?php echo esc_attr($option); ?>" style="width: 50%;" />
        <?php
    }
    
  

    // RANGOS
    public function rango_master_callback() {
        $rango_master = get_option('rango_master', '');
        $rango_bronce = get_option('rango_bronce', '');
        $rango_bronce_elite = get_option('rango_bronce_elite', '');
        $rango_plata = get_option('rango_plata', '');
        $rango_plata_elite = get_option('rango_plata_elite', '');
        $rango_oro = get_option('rango_oro', '');
        $rango_oro_elite = get_option('rango_oro_elite', '');
        $rango_diamante = get_option('rango_diamante', '');
        $rango_socio = get_option('rango_socio', '');
        ?>
            <label for="rango_master">Master</label>
        <br>
            <input type="text" id="rango_master" name="rango_master" value="<?php echo esc_attr($rango_master); ?>" style="width: 50%;" />
        <br>
        <br>
            <label for="rango_bronce">Bronce</label><br>
            <input type="text" id="rango_bronce" name="rango_bronce" value="<?php echo esc_attr($rango_bronce); ?>" style="width: 50%;" />
        <br>
        <br>
            <label for="rango_bronce_elite">Bronce Élite</label><br>
            <input type="text" id="rango_bronce_elite" name="rango_bronce_elite" value="<?php echo esc_attr($rango_bronce_elite); ?>" style="width: 50%;" />
        <br>
        <br>
            <label for="rango_plata">Plata</label><br>
            <input type="text" id="rango_plata" name="rango_plata" value="<?php echo esc_attr($rango_plata); ?>" style="width: 50%;" />
        <br>
        <br>
            <label for="rango_plata_elite">Plata Elite</label><br>
            <input type="text" id="rango_plata_elite" name="rango_plata_elite" value="<?php echo esc_attr($rango_plata_elite); ?>" style="width: 50%;" />
        <br>
        <br>
            <label for="rango_oro">Oro</label><br>
            <input type="text" id="rango_oro" name="rango_oro" value="<?php echo esc_attr($rango_oro); ?>" style="width: 50%;" />
        <br>
        <br>
            <label for="rango_oro_elite">Oro élite</label><br>
            <input type="text" id="rango_oro_elite" name="rango_oro_elite" value="<?php echo esc_attr($rango_oro_elite); ?>" style="width: 50%;" />
        <br>
        <br>
            <label for="rango_diamante">Diamante</label><br>
            <input type="text" id="rango_diamante" name="rango_diamante" value="<?php echo esc_attr($rango_diamante); ?>" style="width: 50%;" />
        <br>
        <br>
            <label for="rango_socio">Rango Por defecto Socio</label><br>
            <input type="text" id="rango_socio" name="rango_socio" value="<?php echo esc_attr($rango_socio); ?>" style="width: 50%;" />
        <?php
    }



    public function gallery_images_callback() {
        $gallery_images = get_option('gallery_images', '');
        ?>
        <input id="gallery_images" type="hidden" name="gallery_images" value="<?php echo esc_attr($gallery_images); ?>" />
        <div id="gallery_images_display"></div>
        <button type="button" class="button" onclick="openMediaUploader()">Seleccionar Imágenes</button>
        <script>
        function openMediaUploader() {
            var mediaUploader;
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media({
                title: 'Seleccionar Imágenes',
                button: {
                    text: 'Usar estas imágenes'
                },
                multiple: true
            });
            mediaUploader.on('select', function() {
                var attachments = mediaUploader.state().get('selection').map(
                    function(attachment) {
                        attachment.toJSON();
                        return attachment;
                    }
                );
                var ids = attachments.map(function(item) {
                    return item.id;
                });
                jQuery('#gallery_images').val(ids.join(','));
                updateImagesDisplay();
            });
            mediaUploader.open();
        }
    
        function updateImagesDisplay() {
            var ids = jQuery('#gallery_images').val().split(',');
            if(ids.length > 0 && ids[0] !== "") {
                jQuery.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'get_gallery_image_urls',
                        ids: ids.join(',')
                    },
                    success: function(response) {
                        var display = jQuery('#gallery_images_display');
                        display.empty();
                        if(response.success) {
                            response.data.forEach(function(url) {
                                display.append('<img src="' + url + '" style="max-width: 100px; margin-right: 10px;">');
                            });
                        }
                    }
                });
            }
        }
    
        jQuery(document).ready(function() {
            updateImagesDisplay();
        });
        </script>
        <?php
    }
    
}
