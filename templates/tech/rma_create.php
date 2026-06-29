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
$pageTitle = Helpers::__('tech_create_title');
?>

<div style="max-width: 750px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <a href="index.php?route=tech/dashboard" style="font-weight: 500; font-size: 0.9rem;">&larr; <?php echo Helpers::__('btn_back_dashboard'); ?></a>
        <h2 style="margin-top: 10px; margin-bottom: 5px;"><?php echo Helpers::__('tech_create_heading'); ?></h2>
        <p style="color: var(--text-secondary); font-size: 0.95rem;"><?php echo Helpers::__('tech_create_desc'); ?></p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="card" style="background-color: var(--color-error-bg); border-color: var(--color-error); color: var(--color-error); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 500;">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?route=tech/rma-create" method="post" enctype="multipart/form-data" class="card">
        <!-- Secção 1: Dados do Cliente -->
        <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 20px; font-size: 1.15rem; color: var(--accent-color);">
            <?php echo Helpers::__('sec_client_record'); ?>
        </h3>
        
        <div class="form-group">
            <label class="form-label" for="client_name"><?php echo Helpers::__('lbl_client_name_required'); ?></label>
            <input type="text" name="client_name" id="client_name" class="form-control" required placeholder="<?php echo htmlspecialchars(Helpers::__('ph_client_name_tech')); ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label" for="client_email"><?php echo Helpers::__('lbl_client_email_desc'); ?></label>
                <input type="email" name="client_email" id="client_email" class="form-control" required placeholder="<?php echo htmlspecialchars(Helpers::__('ph_client_email_tech')); ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="client_contact"><?php echo Helpers::__('lbl_client_phone_required'); ?></label>
                <input type="tel" name="client_contact" id="client_contact" class="form-control" required placeholder="<?php echo htmlspecialchars(Helpers::__('ph_client_phone_tech')); ?>">
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                    <input type="checkbox" name="allow_sms_whatsapp" id="allow_sms_whatsapp" value="1" checked style="width: 16px; height: 16px; cursor: pointer;">
                    <label for="allow_sms_whatsapp" style="font-size: 0.82rem; color: var(--text-secondary); cursor: pointer; margin-bottom: 0;">
                        <?php echo Helpers::__('lbl_allow_notifications'); ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="client_address"><?php echo Helpers::__('lbl_client_address_required'); ?></label>
            <textarea name="client_address" id="client_address" class="form-control" rows="2" required placeholder="<?php echo htmlspecialchars(Helpers::__('ph_client_address_tech')); ?>"></textarea>
        </div>

        <!-- Secção 2: Dados do Equipamento -->
        <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-top: 30px; margin-bottom: 20px; font-size: 1.15rem; color: var(--accent-color);">
            <?php echo Helpers::__('sec_device_record'); ?>
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label" for="device_type"><?php echo Helpers::__('lbl_device_type_required'); ?></label>
                <select name="device_type" id="device_type" class="form-control" required>
                    <option value="" disabled selected><?php echo Helpers::__('ph_device_select'); ?></option>
                    <?php foreach ($deviceTypes as $dtype): ?>
                        <option value="<?php echo htmlspecialchars($dtype); ?>"><?php echo htmlspecialchars(Helpers::__($dtype)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="serial_number"><?php echo Helpers::__('lbl_serial_number_imei'); ?></label>
                <input type="text" name="serial_number" id="serial_number" class="form-control" placeholder="<?php echo htmlspecialchars(Helpers::__('ph_serial_tech')); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="device_condition"><?php echo Helpers::__('lbl_device_condition_required'); ?></label>
            <textarea name="device_condition" id="device_condition" class="form-control" rows="3" required placeholder="<?php echo htmlspecialchars(Helpers::__('ph_condition_tech')); ?>"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="attachment"><?php echo Helpers::__('lbl_attachment_optional'); ?></label>
            <div class="custom-file-upload" style="display: flex; align-items: center; gap: 12px; margin-top: 5px;">
                <label for="attachment" class="btn btn-secondary" style="margin: 0; cursor: pointer; padding: 10px 16px; font-size: 0.85rem; height: 38px; display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-input);">
                    📁 <?php echo Helpers::__('btn_choose_file'); ?>
                </label>
                <span id="attachment-file-name" style="color: var(--text-muted); font-size: 0.85rem;">
                    <?php echo Helpers::__('lbl_no_file_chosen'); ?>
                </span>
                <input type="file" name="attachment" id="attachment" style="opacity: 0; width: 0.1px; height: 0.1px; position: absolute; z-index: -1;" accept="image/*,application/pdf" onchange="updateFileName(this, 'attachment-file-name')">
            </div>
        </div>

        <!-- Confirmação -->
        <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center;">
            <p style="color: var(--text-secondary); font-size: 0.85rem;">* <?php echo Helpers::__('required_fields'); ?></p>
            <button type="submit" class="btn btn-primary" id="btn-submit-rma-tech"><?php echo Helpers::__('btn_create_rma_manage'); ?></button>
        </div>
    </form>
</div>

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

