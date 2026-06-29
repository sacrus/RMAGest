<?php
/*
*****************************
*     Desenvolvido Por      *
*      Luis Patricio        *
*      www.lplogic.net      *
* Castelo Branco, Portugal  *
*****************************
*/
?>
<?php
use RmaGest\Helpers;
$pageTitle = Helpers::__('nav_request');

$formMode = Helpers::getSetting('form_mode', 'default');
$customFormJson = Helpers::getSetting('custom_form_json', '');
$fields = [];
if ($formMode === 'custom' && !empty($customFormJson)) {
    $fields = json_decode($customFormJson, true) ?: [];
}
?>

<div style="max-width: 750px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <a href="index.php" style="font-weight: 500; font-size: 0.9rem;">&larr; <?php echo Helpers::__('new_rma_back'); ?></a>
        <h2 style="margin-top: 10px; margin-bottom: 5px;"><?php echo Helpers::__('new_rma_title'); ?></h2>
        <p style="color: var(--text-secondary); font-size: 0.95rem;"><?php echo Helpers::__('new_rma_subtitle'); ?></p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="card" style="background-color: var(--color-error-bg); border-color: var(--color-error); color: var(--color-error); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 500;">
            ⚠️ <?php echo Helpers::__('new_rma_error_fields'); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?route=client/new-rma" method="post" enctype="multipart/form-data" class="card" style="display: flex; flex-direction: column; gap: 20px;">
        <?php if (empty($fields)): ?>
            <!-- Secção 1: Dados do Cliente -->
            <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 20px; font-size: 1.15rem; color: var(--accent-color);">
                <?php echo Helpers::__('sec_contact'); ?>
            </h3>
            
            <div class="form-group">
                <label class="form-label" for="client_name"><?php echo Helpers::__('lbl_client_name'); ?></label>
                <input type="text" name="client_name" id="client_name" class="form-control" required placeholder="<?php echo Helpers::__('ph_client_name'); ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="client_email"><?php echo Helpers::__('lbl_client_email'); ?></label>
                    <input type="email" name="client_email" id="client_email" class="form-control" required placeholder="<?php echo Helpers::__('ph_client_email'); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="client_contact"><?php echo Helpers::__('lbl_client_contact'); ?></label>
                    <input type="tel" name="client_contact" id="client_contact" class="form-control" required placeholder="<?php echo Helpers::__('ph_client_contact'); ?>">
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                        <input type="checkbox" name="allow_sms_whatsapp" id="allow_sms_whatsapp" value="1" checked style="width: 16px; height: 16px; cursor: pointer;">
                        <label for="allow_sms_whatsapp" style="font-size: 0.82rem; color: var(--text-secondary); cursor: pointer; margin-bottom: 0;">
                            <?php echo Helpers::__('lbl_allow_notifications'); ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="client_address"><?php echo Helpers::__('lbl_client_address'); ?></label>
                <textarea name="client_address" id="client_address" class="form-control" rows="2" required placeholder="<?php echo Helpers::__('ph_client_address'); ?>"></textarea>
            </div>

            <!-- Secção 2: Dados do Equipamento -->
            <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-top: 30px; margin-bottom: 20px; font-size: 1.15rem; color: var(--accent-color);">
                <?php echo Helpers::__('sec_device'); ?>
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="device_type"><?php echo Helpers::__('lbl_device_type'); ?></label>
                    <select name="device_type" id="device_type" class="form-control" required>
                        <option value="" disabled selected><?php echo Helpers::__('ph_device_select'); ?></option>
                        <?php foreach ($deviceTypes as $dtype): ?>
                            <option value="<?php echo htmlspecialchars($dtype); ?>"><?php echo htmlspecialchars(Helpers::__($dtype)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="serial_number"><?php echo Helpers::__('lbl_serial_number'); ?></label>
                    <input type="text" name="serial_number" id="serial_number" class="form-control" placeholder="<?php echo Helpers::__('ph_serial'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="device_condition"><?php echo Helpers::__('lbl_device_condition'); ?></label>
                <textarea name="device_condition" id="device_condition" class="form-control" rows="4" required placeholder="<?php echo Helpers::__('ph_condition'); ?>"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="attachment"><?php echo Helpers::__('lbl_attachment'); ?></label>
                <div class="custom-file-upload" style="display: flex; align-items: center; gap: 12px; margin-top: 5px;">
                    <label for="attachment" class="btn btn-secondary" style="margin: 0; cursor: pointer; padding: 10px 16px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-input);">
                        📁 <?php echo Helpers::__('btn_choose_file'); ?>
                    </label>
                    <span id="attachment-file-name" style="color: var(--text-muted); font-size: 0.88rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 250px;">
                        <?php echo Helpers::__('lbl_no_file_chosen'); ?>
                    </span>
                    <input type="file" name="attachment" id="attachment" style="opacity: 0; width: 0.1px; height: 0.1px; position: absolute; z-index: -1;" accept="image/*,application/pdf" onchange="updateFileName(this, 'attachment-file-name')">
                </div>
                <p style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px;"><?php echo Helpers::__('supported_formats'); ?></p>
            </div>

            <!-- Confirmação -->
            <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <p style="color: var(--text-secondary); font-size: 0.85rem;">* <?php echo Helpers::__('required_fields'); ?></p>
                <button type="submit" class="btn btn-primary" id="btn-submit-rma-form"><?php echo Helpers::__('btn_submit_rma'); ?></button>
            </div>
        <?php else: ?>
            <!-- Renderização Dinâmica do Formulário Personalizado -->
            <?php foreach ($fields as $field): 
                $id = $field['id'];
                $label = htmlspecialchars($field['label']);
                $required = !empty($field['required']) ? 'required' : '';
                $type = $field['type'] ?? 'text';
                $mapped = $field['mapped_to'] ?? '';
                
                // Atributo name do input
                $inputName = !empty($mapped) ? $mapped : "custom_field_{$id}";
                
                $options = $field['options'] ?? [];
            ?>
                <div class="form-group">
                    <label class="form-label" for="<?php echo $inputName; ?>">
                        <?php echo $label; ?> <?php echo !empty($required) ? '*' : ''; ?>
                    </label>
                    
                    <?php if ($type === 'text' || $type === 'email' || $type === 'tel'): ?>
                        <input type="<?php echo $type; ?>" name="<?php echo $inputName; ?>" id="<?php echo $inputName; ?>" class="form-control" <?php echo $required; ?> placeholder="">
                        
                    <?php elseif ($type === 'textarea'): ?>
                        <textarea name="<?php echo $inputName; ?>" id="<?php echo $inputName; ?>" class="form-control" rows="3" <?php echo $required; ?>></textarea>
                        
                    <?php elseif ($type === 'select'): ?>
                        <select name="<?php echo $inputName; ?>" id="<?php echo $inputName; ?>" class="form-control" <?php echo $required; ?>>
                            <option value="" disabled selected>Selecione uma opção...</option>
                            <?php 
                            if ($mapped === 'device_type' && empty($options)) {
                                $options = Helpers::getDeviceTypes();
                            }
                            foreach ($options as $opt): 
                            ?>
                                <option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars(Helpers::__($opt)); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                    <?php elseif ($type === 'radio'): ?>
                        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 5px;">
                            <?php foreach ($options as $opt): ?>
                                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="radio" name="<?php echo $inputName; ?>" value="<?php echo htmlspecialchars($opt); ?>" <?php echo $required; ?>>
                                    <span><?php echo htmlspecialchars($opt); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                    <?php elseif ($type === 'checkbox'): ?>
                        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 5px;">
                            <?php foreach ($options as $opt): ?>
                                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="checkbox" name="<?php echo $inputName; ?>[]" value="<?php echo htmlspecialchars($opt); ?>">
                                    <span><?php echo htmlspecialchars($opt); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                    <?php elseif ($type === 'file'): ?>
                        <div class="custom-file-upload" style="display: flex; align-items: center; gap: 12px; margin-top: 5px;">
                            <label for="<?php echo $inputName; ?>" class="btn btn-secondary" style="margin: 0; cursor: pointer; padding: 10px 16px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-input);">
                                📁 <?php echo Helpers::__('btn_choose_file'); ?>
                            </label>
                            <span id="<?php echo $inputName; ?>-file-name" style="color: var(--text-muted); font-size: 0.88rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 250px;">
                                <?php echo Helpers::__('lbl_no_file_chosen'); ?>
                            </span>
                            <input type="file" name="<?php echo $inputName; ?>" id="<?php echo $inputName; ?>" style="opacity: 0; width: 0.1px; height: 0.1px; position: absolute; z-index: -1;" <?php echo $required; ?> accept="image/*,application/pdf" onchange="updateFileName(this, '<?php echo $inputName; ?>-file-name')">
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px;"><?php echo Helpers::__('supported_formats'); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (!formHasField($fields, 'allow_sms_whatsapp')): ?>
                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="allow_sms_whatsapp" id="allow_sms_whatsapp" value="1" checked style="width: 16px; height: 16px; cursor: pointer;">
                    <label for="allow_sms_whatsapp" style="font-size: 0.82rem; color: var(--text-secondary); cursor: pointer; margin-bottom: 0;">
                        <?php echo Helpers::__('lbl_allow_notifications'); ?>
                    </label>
                </div>
            <?php endif; ?>

            <!-- Botão de Envio -->
            <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <p style="color: var(--text-secondary); font-size: 0.85rem;">* <?php echo Helpers::__('required_fields'); ?></p>
                <button type="submit" class="btn btn-primary" id="btn-submit-rma-form"><?php echo Helpers::__('btn_submit_rma'); ?></button>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php
if (!function_exists('formHasField')) {
    function formHasField($fields, $mappedName) {
        foreach ($fields as $f) {
            if (($f['mapped_to'] ?? '') === $mappedName) return true;
        }
        return false;
    }
}
?>

<script>
function updateFileName(input, targetId) {
    var label = document.getElementById(targetId);
    if (input.files && input.files.length > 0) {
        label.textContent = input.files[0].name;
        label.style.color = "var(--text-main)";
    } else {
        label.textContent = "<?php echo addslashes(Helpers::__('lbl_no_file_chosen')); ?>";
        label.style.color = "var(--text-muted)";
    }
}
</script>

