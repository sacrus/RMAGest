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
use RmaGest\Auth;
use RmaGest\Helpers;
$pageTitle = Helpers::__('settings_title');

$availablePermissions = Auth::getAvailablePermissions();
$isAdmin = Auth::isAdmin();
$canManageSettings = Auth::checkPermission('manage_settings');
$canManageUsers = Auth::checkPermission('manage_users');
?>

<div style="margin-bottom: 24px;">
    <h2><?php echo Helpers::__('settings_title'); ?></h2>
    <p style="color: var(--text-secondary); font-size: 0.95rem;"><?php echo Helpers::__('settings_desc'); ?></p>
</div>

<?php if (!empty($success)): ?>
    <div class="card" style="background-color: var(--color-success-bg); border-color: var(--color-success); color: var(--color-success); padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 500; font-size:0.95rem;">
        ✓ <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="card" style="background-color: var(--color-error-bg); border-color: var(--color-error); color: var(--color-error); padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 500; font-size:0.95rem;">
        ⚠️ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;" id="settings-grid">
    <!-- Menu Lateral Esquerdo: Navegação de Definições -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div class="card" style="padding: 16px;">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px;">
                <li><button onclick="switchTab('general')" class="btn btn-secondary tab-btn active" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-general"><?php echo Helpers::__('tab_general'); ?></button></li>
                <li><button onclick="switchTab('email')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-email"><?php echo Helpers::__('tab_email'); ?></button></li>
                <li><button onclick="switchTab('messages')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-messages"><?php echo Helpers::__('tab_messages'); ?></button></li>
                <li><button onclick="switchTab('statuses')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-statuses"><?php echo Helpers::__('tab_statuses'); ?></button></li>
                <li><button onclick="switchTab('device-types')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-device-types"><?php echo Helpers::__('tab_device_types'); ?></button></li>
                <li><button onclick="switchTab('users')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-users"><?php echo Helpers::__('tab_users'); ?></button></li>
                <li><button onclick="switchTab('integration')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-integration"><?php echo Helpers::__('tab_integration'); ?></button></li>
                <li><button onclick="switchTab('form-builder')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-form-builder"><?php echo Helpers::__('tab_form_builder'); ?></button></li>
                <li><button onclick="switchTab('notifications')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-notifications"><?php echo Helpers::__('tab_notifications'); ?></button></li>
                <li><button onclick="switchTab('backups')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-backups"><?php echo Helpers::__('tab_backups'); ?></button></li>
                <li><button onclick="switchTab('predefined-responses')" class="btn btn-secondary tab-btn" style="width:100%; text-align:left; justify-content:flex-start;" id="tab-btn-predefined-responses"><?php echo Helpers::__('tab_predefined_responses'); ?></button></li>
            </ul>
        </div>
        
        <div class="card" style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
            <?php echo Helpers::__('settings_help_gdpr'); ?>
        </div>
    </div>

    <!-- Bloco Direito: Conteúdo das Abas -->
    <div>
        <!-- ABA 1: DEFINIÇÕES GERAIS -->
        <div id="tab-general" class="tab-content">
            <form action="index.php?route=tech/settings&action=general" method="post" enctype="multipart/form-data" class="card">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('tab_general'); ?></h3>
                
                <div class="form-group">
                    <label class="form-label" for="set_site_name"><?php echo Helpers::__('lbl_site_logo'); ?></label>
                    <input type="text" name="site_name" id="set_site_name" class="form-control" required value="<?php echo htmlspecialchars($settings['site_name']); ?>" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                </div>

                <!-- Logótipo -->
                <div class="form-group">
                    <label class="form-label"><?php echo Helpers::__('lbl_logo_update'); ?></label>
                    <?php if (!empty($settings['logo_path'])): ?>
                        <div style="display:flex; align-items:center; gap: 15px; margin-bottom: 12px;">
                            <img src="<?php echo htmlspecialchars($settings['logo_path']); ?>" alt="Logo" style="max-height: 45px; border-radius: 4px; background: rgba(255,255,255,0.05); padding: 5px;">
                            <span style="font-size:0.8rem; color:var(--text-muted);"><?php echo Helpers::__('lbl_logo_active'); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="file" name="logo" id="set_logo" style="display: none;" accept="image/*" onchange="updateFileName(this, 'logo-file-name')" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                        <label for="set_logo" class="btn btn-secondary" style="padding: 10px 16px; font-size: 0.85rem; height: 38px; cursor: <?php echo $canManageSettings ? 'pointer' : 'not-allowed'; ?>; margin-bottom: 0;">
                            📁 <?php echo Helpers::__('btn_choose_file'); ?>
                        </label>
                        <span id="logo-file-name" style="font-size: 0.85rem; color: var(--text-secondary);"><?php echo Helpers::__('lbl_no_file_chosen'); ?></span>
                    </div>
                </div>

                <!-- Ícone do Site (Favicon) -->
                <div class="form-group" style="margin-top: 25px;">
                    <label class="form-label"><?php echo Helpers::__('lbl_favicon_update'); ?></label>
                    <?php if (!empty($settings['favicon_path'])): ?>
                        <div style="display:flex; align-items:center; gap: 15px; margin-bottom: 12px;">
                            <img src="<?php echo htmlspecialchars($settings['favicon_path']); ?>" alt="Favicon" style="max-height: 32px; max-width: 32px; border-radius: 4px; background: rgba(255,255,255,0.05); padding: 5px;">
                            <span style="font-size:0.8rem; color:var(--text-muted);"><?php echo Helpers::__('lbl_favicon_active'); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="file" name="favicon" id="set_favicon" style="display: none;" accept="image/x-icon,image/png,image/jpeg,image/webp" onchange="updateFileName(this, 'favicon-file-name')" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                        <label for="set_favicon" class="btn btn-secondary" style="padding: 10px 16px; font-size: 0.85rem; height: 38px; cursor: <?php echo $canManageSettings ? 'pointer' : 'not-allowed'; ?>; margin-bottom: 0;">
                            📁 <?php echo Helpers::__('btn_choose_file'); ?>
                        </label>
                        <span id="favicon-file-name" style="font-size: 0.85rem; color: var(--text-secondary);"><?php echo Helpers::__('lbl_no_file_chosen'); ?></span>
                    </div>
                </div>

                <?php if ($canManageSettings): ?>
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-general-settings"><?php echo Helpers::__('btn_save_settings'); ?></button>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- ABA 2: DEFINIÇÕES DE EMAIL -->
        <div id="tab-email" class="tab-content" style="display: none;">
            <form action="index.php?route=tech/settings&action=email" method="post" class="card">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('lbl_smtp_title'); ?></h3>
                
                <div class="form-group" style="display:flex; align-items:center; gap: 10px; margin-bottom: 20px;">
                    <input type="checkbox" name="smtp_enabled" id="set_smtp_enabled" value="1" <?php echo $settings['smtp_enabled'] === '1' ? 'checked' : ''; ?> onchange="toggleSettingsSmtp()" style="width: 18px; height: 18px; cursor: pointer;" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                    <label for="set_smtp_enabled" class="form-label" style="margin-bottom:0; cursor: pointer;"><?php echo Helpers::__('lbl_smtp_enabled_check'); ?></label>
                </div>

                <div id="settings_smtp_fields" style="<?php echo $settings['smtp_enabled'] === '1' ? 'block' : 'none'; ?>">
                    <div class="form-group">
                        <label class="form-label" for="set_smtp_host"><?php echo Helpers::__('lbl_smtp_host'); ?></label>
                        <input type="text" name="smtp_host" id="set_smtp_host" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>" placeholder="smtp.example.com" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label" for="set_smtp_port"><?php echo Helpers::__('lbl_smtp_port'); ?></label>
                            <input type="text" name="smtp_port" id="set_smtp_port" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="set_smtp_encryption"><?php echo Helpers::__('lbl_smtp_enc'); ?></label>
                            <select name="smtp_encryption" id="set_smtp_encryption" class="form-control" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                                <option value="tls" <?php echo $settings['smtp_encryption'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo $settings['smtp_encryption'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="none" <?php echo $settings['smtp_encryption'] === 'none' ? 'selected' : ''; ?>><?php echo Helpers::__('opt_none'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label" for="set_smtp_user"><?php echo Helpers::__('lbl_smtp_user'); ?></label>
                            <input type="text" name="smtp_user" id="set_smtp_user" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>" placeholder="name@example.com" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="set_smtp_pass"><?php echo Helpers::__('lbl_smtp_pass'); ?></label>
                            <input type="password" name="smtp_pass" id="set_smtp_pass" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>" placeholder="••••••••" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label" for="set_smtp_from_email"><?php echo Helpers::__('lbl_smtp_from_email'); ?></label>
                            <input type="email" name="smtp_from_email" id="set_smtp_from_email" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_from_email'] ?? ''); ?>" placeholder="noreply@oficina.com" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="set_smtp_from_name"><?php echo Helpers::__('lbl_smtp_from_name'); ?></label>
                            <input type="text" name="smtp_from_name" id="set_smtp_from_name" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_from_name'] ?? ''); ?>" placeholder="Oficina RMA Gest" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <p style="color:var(--text-muted); font-size:0.8rem; margin-top:15px; padding-top:10px; border-top:1px solid var(--border-color);">
                    <?php echo Helpers::__('lbl_smtp_disclaimer'); ?>
                </p>

                <?php if ($canManageSettings): ?>
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-email-settings"><?php echo Helpers::__('btn_save_settings'); ?></button>
                    </div>
                <?php endif; ?>
            </form>

            <!-- Modelos de Email por Idioma -->
            <form action="index.php?route=tech/settings&action=templates" method="post" class="card" style="margin-top: 30px;">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;"><?php echo Helpers::__('lbl_msg_templates_title'); ?> (Email)</h3>
                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:20px;"><?php echo Helpers::__('lbl_msg_templates_desc'); ?></p>
                
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 15px; margin-bottom: 25px;">
                    <p style="font-size: 0.85rem; margin-bottom: 0; line-height: 1.4; color: var(--accent-color);">
                        <?php echo Helpers::__('lbl_tpl_placeholders_help'); ?>
                    </p>
                </div>

                <?php 
                $tplTypes = [
                    'client_new' => 'lbl_tpl_client_new',
                    'tech_create' => 'lbl_tpl_tech_create',
                    'status_update' => 'lbl_tpl_status_update',
                    'chat_new' => 'lbl_tpl_chat_new'
                ];
                
                foreach ($tplTypes as $tKey => $tLabel): 
                ?>
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px; background: rgba(0,0,0,0.08);">
                        <h4 style="margin-bottom: 15px; color: var(--text-main); font-size: 1.05rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px;">
                            <?php echo Helpers::__( $tLabel ); ?>
                        </h4>

                        <div class="form-group">
                            <label class="form-label" style="font-size: 0.78rem;"><?php echo Helpers::__('lbl_tpl_subject'); ?></label>
                            <input type="text" name="email_tpl_<?php echo $tKey; ?>_subject" class="form-control" style="font-size:0.85rem;" value="<?php echo htmlspecialchars(Helpers::getSetting("email_tpl_{$tKey}_subject", Helpers::getSetting("email_tpl_{$tKey}_subject_pt", Helpers::__("default_email_{$tKey}_subject")))); ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.78rem;"><?php echo Helpers::__('lbl_tpl_body'); ?></label>
                            <textarea name="email_tpl_<?php echo $tKey; ?>_body" class="form-control" rows="4" style="font-size:0.85rem; font-family:monospace;"><?php echo htmlspecialchars(Helpers::getSetting("email_tpl_{$tKey}_body", Helpers::getSetting("email_tpl_{$tKey}_body_pt", Helpers::__("default_email_{$tKey}_body")))); ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($canManageSettings): ?>
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-email-templates"><?php echo Helpers::__('btn_save_templates'); ?></button>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- ABA 3: MENSAGENS E TEMPLATES E WHATSAPP -->
        <div id="tab-messages" class="tab-content" style="display: none;">
            <!-- 1. Configuração do WhatsApp API -->
            <form action="index.php?route=tech/settings&action=whatsapp" method="post" class="card" style="margin-bottom: 30px;">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('lbl_whatsapp_title'); ?></h3>
                
                <div class="form-group" style="display:flex; align-items:center; gap: 10px; margin-bottom: 20px;">
                    <input type="checkbox" name="whatsapp_enabled" id="set_whatsapp_enabled" value="1" <?php echo $settings['whatsapp_enabled'] === '1' ? 'checked' : ''; ?> style="width: 18px; height: 18px; cursor: pointer;" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                    <label for="set_whatsapp_enabled" class="form-label" style="margin-bottom:0; cursor: pointer;"><?php echo Helpers::__('lbl_whatsapp_enabled'); ?></label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="set_whatsapp_url"><?php echo Helpers::__('lbl_whatsapp_url'); ?></label>
                    <input type="text" name="whatsapp_url" id="set_whatsapp_url" class="form-control" value="<?php echo htmlspecialchars($settings['whatsapp_url'] ?? ''); ?>" placeholder="https://api.whatsapp-gateway.com/send" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="set_whatsapp_token_header"><?php echo Helpers::__('lbl_whatsapp_token_header'); ?></label>
                        <input type="text" name="whatsapp_token_header" id="set_whatsapp_token_header" class="form-control" value="<?php echo htmlspecialchars($settings['whatsapp_token_header'] ?? 'Authorization'); ?>" placeholder="Authorization" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="set_whatsapp_token"><?php echo Helpers::__('lbl_whatsapp_token'); ?></label>
                        <input type="text" name="whatsapp_token" id="set_whatsapp_token" class="form-control" value="<?php echo htmlspecialchars($settings['whatsapp_token'] ?? ''); ?>" placeholder="API Token / Key" <?php echo !$canManageSettings ? 'disabled' : ''; ?>>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="set_whatsapp_payload"><?php echo Helpers::__('lbl_whatsapp_payload'); ?></label>
                    <textarea name="whatsapp_payload" id="set_whatsapp_payload" class="form-control" rows="2" <?php echo !$canManageSettings ? 'disabled' : ''; ?>><?php echo htmlspecialchars($settings['whatsapp_payload'] ?? '{"number": "{phone}", "message": "{message}"}'); ?></textarea>
                    <p style="color:var(--text-muted); font-size:0.75rem; margin-top:4px;"><?php echo Helpers::__('lbl_whatsapp_payload_desc'); ?></p>
                </div>

                <?php if ($canManageSettings): ?>
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-whatsapp-settings"><?php echo Helpers::__('btn_save_whatsapp'); ?></button>
                    </div>
                <?php endif; ?>
            </form>

            <!-- 2. Modelos de WhatsApp por Idioma -->
            <form action="index.php?route=tech/settings&action=templates" method="post" class="card">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;"><?php echo Helpers::__('lbl_msg_templates_title'); ?> (WhatsApp)</h3>
                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:20px;"><?php echo Helpers::__('lbl_msg_templates_desc'); ?></p>
                
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 15px; margin-bottom: 25px;">
                    <p style="font-size: 0.85rem; margin-bottom: 0; line-height: 1.4; color: var(--accent-color);">
                        <?php echo Helpers::__('lbl_tpl_placeholders_help'); ?>
                    </p>
                </div>

                <?php 
                foreach ($tplTypes as $tKey => $tLabel): 
                ?>
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 20px; background: rgba(0,0,0,0.08);">
                        <h4 style="margin-bottom: 15px; color: var(--text-main); font-size: 1.05rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px;">
                            <?php echo Helpers::__( $tLabel ); ?>
                        </h4>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.78rem;"><?php echo Helpers::__('lbl_tpl_wa_body'); ?></label>
                            <textarea name="whatsapp_tpl_<?php echo $tKey; ?>" class="form-control" rows="3" style="font-size:0.85rem;"><?php echo htmlspecialchars(Helpers::getSetting("whatsapp_tpl_{$tKey}", Helpers::getSetting("whatsapp_tpl_{$tKey}_pt", Helpers::__("default_whatsapp_{$tKey}")))); ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($canManageSettings): ?>
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-whatsapp-templates"><?php echo Helpers::__('btn_save_templates'); ?></button>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- ABA 4: ESTADOS DE RMA -->
        <div id="tab-statuses" class="tab-content" style="display: none;">
            <div class="card">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('lbl_statuses_flow'); ?></h3>
                
                <!-- Lista de Estados Atuais -->
                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:15px;"><?php echo Helpers::__('lbl_statuses_desc'); ?></p>
                
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; margin-bottom: 25px;" id="status-list-settings">
                    <?php foreach ($rmaStatuses as $index => $status): ?>
                        <li style="display:flex; justify-content:space-between; align-items:center; background-color: var(--bg-card); border: 1px solid var(--border-color); padding: 10px 16px; border-radius: var(--radius-sm);">
                            <div style="display:flex; align-items:center; gap: 10px;">
                                <span style="color: var(--text-muted); font-weight:700;"><?php echo $index + 1; ?>.</span>
                                <strong><?php echo htmlspecialchars(Helpers::__($status)); ?></strong>
                            </div>
                            
                            <?php if ($canManageSettings): ?>
                                <form action="index.php?route=tech/settings&action=status-delete" method="post">
                                    <input type="hidden" name="status_name" value="<?php echo htmlspecialchars($status); ?>">
                                    <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem; background-color: var(--color-error-bg); color: var(--color-error); border-color:transparent;" title="Remover Estado">❌</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Adicionar Novo Estado -->
                <?php if ($canManageSettings): ?>
                    <form action="index.php?route=tech/settings&action=status-add" method="post" style="border-top:1px solid var(--border-color); padding-top:20px; display:flex; gap:10px; align-items:end;">
                        <div class="form-group" style="margin-bottom:0; flex:1;">
                            <label class="form-label" for="new_status_input" style="font-size:0.8rem;"><?php echo Helpers::__('lbl_add_status'); ?></label>
                            <input type="text" name="new_status" id="new_status_input" class="form-control" placeholder="Ex: Em Aguardo de Peças Externa" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="height: 42px;"><?php echo Helpers::__('btn_add_part'); ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- ABA 5: TIPOS DE EQUIPAMENTO -->
        <div id="tab-device-types" class="tab-content" style="display: none;">
            <div class="card">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('tab_device_types'); ?></h3>

                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:15px;"><?php echo Helpers::__('lbl_device_types_desc'); ?></p>

                <!-- Lista de Tipos Atuais -->
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; margin-bottom: 25px;" id="device-types-list">
                    <?php foreach ($deviceTypes as $index => $dtype): ?>
                        <li style="display:flex; justify-content:space-between; align-items:center; background-color: var(--bg-card); border: 1px solid var(--border-color); padding: 10px 16px; border-radius: var(--radius-sm);" id="dtype-row-<?php echo $index; ?>">
                            <!-- Modo visualização -->
                            <div class="dtype-view" style="display:flex; align-items:center; gap: 10px; flex:1;">
                                <span style="color: var(--text-muted); font-weight:700;"><?php echo $index + 1; ?>.</span>
                                <strong id="dtype-label-<?php echo $index; ?>"><?php echo htmlspecialchars(Helpers::__($dtype)); ?></strong>
                            </div>
                            <!-- Modo edição (oculto por defeito) -->
                            <form class="dtype-edit" action="index.php?route=tech/settings&action=device-type-edit" method="post" style="display:none; flex:1; gap:8px; align-items:center;">
                                <input type="hidden" name="old_name" value="<?php echo htmlspecialchars($dtype); ?>">
                                <input type="text" name="new_name" class="form-control" value="<?php echo htmlspecialchars($dtype); ?>" required style="flex:1; padding:6px 10px; font-size:0.9rem;">
                                <button type="submit" class="btn btn-primary" style="padding:5px 12px; font-size:0.8rem;"><?php echo Helpers::__('btn_save_item'); ?></button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEditDtype(<?php echo $index; ?>)" style="padding:5px 10px; font-size:0.8rem;"><?php echo Helpers::__('Cancelar'); ?></button>
                            </form>
                            <?php if ($canManageSettings): ?>
                                <div class="dtype-actions" style="display:flex; gap:6px; margin-left:10px;">
                                    <button type="button" onclick="editDtype(<?php echo $index; ?>)" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.75rem;" title="Editar">✏️</button>
                                    <form action="index.php?route=tech/settings&action=device-type-delete" method="post" onsubmit="return confirm('Eliminar este tipo de equipamento?');">
                                        <input type="hidden" name="dtype_name" value="<?php echo htmlspecialchars($dtype); ?>">
                                        <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem; background-color: var(--color-error-bg); color: var(--color-error); border-color:transparent;" title="Eliminar">❌</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($deviceTypes)): ?>
                        <li style="color: var(--text-muted); font-size:0.9rem; padding: 10px 0; text-align:center;">Nenhum tipo configurado.</li>
                    <?php endif; ?>
                </ul>

                <!-- Adicionar Novo Tipo -->
                <?php if ($canManageSettings): ?>
                    <form action="index.php?route=tech/settings&action=device-type-add" method="post" style="border-top:1px solid var(--border-color); padding-top:20px; display:flex; gap:10px; align-items:end;">
                        <div class="form-group" style="margin-bottom:0; flex:1;">
                            <label class="form-label" for="new_dtype_input" style="font-size:0.8rem;"><?php echo Helpers::__('lbl_add_device_type'); ?></label>
                            <input type="text" name="new_dtype" id="new_dtype_input" class="form-control" placeholder="Ex: Impressora, Monitor, Router..." required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="height: 42px;"><?php echo Helpers::__('btn_add_part'); ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- ABA 6: GESTÃO DE UTILIZADORES & PERMISSÕES -->
        <div id="tab-users" class="tab-content" style="display: none;">
            
            <!-- Lista de Técnicos / Utilizadores -->
            <div class="card" style="margin-bottom: 30px;">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('lbl_users_title'); ?></h3>
                
                <div class="table-responsive">
                    <table class="custom-table" style="font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th><?php echo Helpers::__('tbl_name'); ?></th>
                                <th><?php echo Helpers::__('tbl_username'); ?></th>
                                <th><?php echo Helpers::__('tbl_email'); ?></th>
                                <th><?php echo Helpers::__('tbl_role'); ?></th>
                                <th style="text-align: right;"><?php echo Helpers::__('tbl_actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                                    <td><span style="font-family:monospace;"><?php echo htmlspecialchars($u['username']); ?></span></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $u['role'] === 'admin' ? 'badge-success' : 'badge-info'; ?>">
                                            <?php echo Helpers::__($u['role'] === 'admin' ? 'Administrador' : 'Técnico'); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php if ($canManageUsers && $_SESSION['user_id'] != $u['id']): ?>
                                            <form action="index.php?route=tech/settings&action=user-delete" method="post" onsubmit="return confirm('Deseja eliminar permanentemente esta conta técnica?');" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem; background-color: var(--color-error-bg); color: var(--color-error); border-color:transparent;" title="Eliminar Utilizador">🗑️</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size:0.75rem; color:var(--text-muted); font-style:italic;">Atual (Bloqueado)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulário: Criar Novo Técnico -->
            <?php if ($canManageUsers): ?>
                <form action="index.php?route=tech/settings&action=user-add" method="post" class="card">
                    <h3 style="font-size: 1.1rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('lbl_users_add_title'); ?></h3>
                    
                    <div class="form-group">
                        <label class="form-label" for="u_name">Nome Completo *</label>
                        <input type="text" name="name" id="u_name" class="form-control" required placeholder="Ex: Rodrigo Pereira">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label" for="u_username">Nome de Utilizador * (Username)</label>
                            <input type="text" name="username" id="u_username" class="form-control" required placeholder="Ex: rodrigo.tech">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="u_email">Email Técnico *</label>
                            <input type="email" name="email" id="u_email" class="form-control" required placeholder="Ex: rodrigo@oficina.com">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label" for="u_password">Palavra-passe *</label>
                            <input type="password" name="password" id="u_password" class="form-control" required minlength="6" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="u_role">Papel do Sistema (Role) *</label>
                            <select name="role" id="u_role" class="form-control" onchange="togglePermissionsDiv(this)">
                                <option value="tech" selected><?php echo Helpers::__('Técnico'); ?> (<?php echo Helpers::__('tech_desc'); ?>)</option>
                                <option value="admin"><?php echo Helpers::__('Administrador'); ?> (<?php echo Helpers::__('admin_desc'); ?>)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Escolha de Permissões para Técnicos -->
                    <div id="permissions-selector-wrapper" style="margin-top: 15px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 15px; background: rgba(0,0,0,0.05);">
                        <h4 style="font-size:0.9rem; margin-bottom:10px;"><?php echo Helpers::__('lbl_permissions_selector'); ?></h4>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <?php foreach ($availablePermissions as $key => $label): ?>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>" id="perm_<?php echo $key; ?>" checked style="width:16px; height:16px; cursor:pointer;">
                                    <label for="perm_<?php echo $key; ?>" style="font-size:0.8rem; cursor:pointer; color:var(--text-secondary);"><?php echo htmlspecialchars(Helpers::__($key)); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="color:var(--text-muted); font-size:0.75rem; margin-top:10px;"><?php echo Helpers::__('lbl_permissions_admin_desc'); ?></p>
                    </div>

                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-user"><?php echo Helpers::__('btn_create_tech'); ?></button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- ABA 7: CÓDIGO DE INTEGRAÇÃO -->
        <div id="tab-integration" class="tab-content" style="display: none;">
            <div class="card">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('lbl_integration_title'); ?></h3>
                <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 25px;"><?php echo Helpers::__('lbl_integration_desc'); ?></p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;" id="widget-generator-layout">
                    <!-- Configurações do Widget -->
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="widget_url"><?php echo Helpers::__('lbl_rma_url'); ?></label>
                            <?php 
                            $defaultUrl = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF']) . "/";
                            ?>
                            <input type="text" id="widget_url" class="form-control" value="<?php echo htmlspecialchars($defaultUrl); ?>" placeholder="https://exemplo.com/rma/">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="widget_label"><?php echo Helpers::__('lbl_widget_label'); ?></label>
                            <input type="text" id="widget_label" class="form-control" value="Pesquisar Estado de Reparação">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="widget_placeholder"><?php echo Helpers::__('lbl_widget_placeholder'); ?></label>
                            <input type="text" id="widget_placeholder" class="form-control" value="Nº RMA (Ex: RMA-2026-12345)">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="widget_btn_text"><?php echo Helpers::__('lbl_widget_btn_text'); ?></label>
                            <input type="text" id="widget_btn_text" class="form-control" value="Procurar">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" for="widget_target"><?php echo Helpers::__('lbl_widget_target'); ?></label>
                                <select id="widget_target" class="form-control">
                                    <option value="_blank" selected><?php echo Helpers::__('opt_new_tab'); ?></option>
                                    <option value="_self"><?php echo Helpers::__('opt_same_tab'); ?></option>
                                </select>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" for="widget_bg_style"><?php echo Helpers::__('lbl_widget_bg_style'); ?></label>
                                <select id="widget_bg_style" class="form-control">
                                    <option value="glass" selected><?php echo Helpers::__('opt_dark_glass'); ?></option>
                                    <option value="light"><?php echo Helpers::__('opt_light'); ?></option>
                                    <option value="transparent"><?php echo Helpers::__('opt_transparent'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="widget_accent_color"><?php echo Helpers::__('lbl_widget_accent_color'); ?></label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="color" id="widget_accent_color" value="#6366f1" style="width: 50px; height: 38px; padding: 0; border: 1px solid var(--border-color); border-radius: 4px; cursor: pointer; background: transparent;">
                                <input type="text" id="widget_accent_color_text" class="form-control" value="#6366f1" style="font-family: monospace;">
                            </div>
                        </div>
                    </div>

                    <!-- Visualização e Código -->
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div>
                            <h4 style="font-size: 0.95rem; margin-bottom: 12px; color: var(--accent-color);"><?php echo Helpers::__('lbl_preview'); ?></h4>
                            <div id="widget-preview-container" style="background: rgba(0,0,0,0.15); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 30px 20px; display: flex; align-items: center; justify-content: center; min-height: 200px;">
                                <!-- O preview será injetado por JS -->
                            </div>
                        </div>
                        
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <h4 style="font-size: 0.95rem; margin-bottom: 0; color: var(--accent-color);"><?php echo Helpers::__('lbl_generated_code'); ?></h4>
                                <button type="button" onclick="copyWidgetCode()" class="btn btn-secondary" id="btn-copy-widget-code" style="padding: 4px 12px; font-size: 0.78rem; height: 28px;">
                                    📋 <?php echo Helpers::__('btn_copy_code'); ?>
                                </button>
                            </div>
                            <textarea id="widget_code_output" class="form-control" rows="8" readonly style="font-family: monospace; font-size: 0.82rem; background: rgba(0,0,0,0.25); color: #818cf8; resize: none;"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ABA 8: BUILDER DO FORMULÁRIO -->
        <div id="tab-form-builder" class="tab-content" style="display: none;">
            <form action="index.php?route=tech/settings&action=form-builder" method="post" id="form-builder-settings-form" class="card">
                <input type="hidden" name="custom_form_json" id="custom_form_json_input">
                
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:20px;"><?php echo Helpers::__('lbl_form_builder_title'); ?></h3>
                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:20px;"><?php echo Helpers::__('lbl_form_builder_desc'); ?></p>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label class="form-label" style="font-weight: 700; font-size: 0.95rem;"><?php echo Helpers::__('lbl_form_mode'); ?></label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="form_mode" value="default" <?php echo Helpers::getSetting('form_mode', 'default') === 'default' ? 'checked' : ''; ?> onchange="toggleFormBuilderDisplay(this.value)">
                            <span><?php echo Helpers::__('lbl_form_mode_default'); ?></span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="form_mode" value="custom" <?php echo Helpers::getSetting('form_mode', 'default') === 'custom' ? 'checked' : ''; ?> onchange="toggleFormBuilderDisplay(this.value)">
                            <span><?php echo Helpers::__('lbl_form_mode_custom'); ?></span>
                        </label>
                    </div>
                </div>

                <!-- Painel Drag & Drop -->
                <div id="drag-drop-builder-container" style="display: <?php echo Helpers::getSetting('form_mode', 'default') === 'custom' ? 'grid' : 'none'; ?>; grid-template-columns: 1fr 2fr; gap: 20px; margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                    <!-- Elementos Disponíveis -->
                    <div>
                        <h4 style="margin-bottom: 12px; font-size: 0.9rem; color: var(--accent-color);"><?php echo Helpers::__('lbl_toolbox_header'); ?></h4>
                        <div style="display: flex; flex-direction: column; gap: 8px;" id="builder-toolbox">
                            <div class="toolbox-item" draggable="true" data-type="text" style="padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; cursor: grab; background: rgba(255,255,255,0.02); display: flex; align-items: center; gap: 10px; font-size: 0.85rem;" title="Clique ou arraste para adicionar">
                                <span>📝</span> <strong><?php echo Helpers::__('lbl_tb_text'); ?></strong>
                            </div>
                            <div class="toolbox-item" draggable="true" data-type="textarea" style="padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; cursor: grab; background: rgba(255,255,255,0.02); display: flex; align-items: center; gap: 10px; font-size: 0.85rem;" title="Clique ou arraste para adicionar">
                                <span>📖</span> <strong><?php echo Helpers::__('lbl_tb_textarea'); ?></strong>
                            </div>
                            <div class="toolbox-item" draggable="true" data-type="select" style="padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; cursor: grab; background: rgba(255,255,255,0.02); display: flex; align-items: center; gap: 10px; font-size: 0.85rem;" title="Clique ou arraste para adicionar">
                                <span>▼</span> <strong><?php echo Helpers::__('lbl_tb_select'); ?></strong>
                            </div>
                            <div class="toolbox-item" draggable="true" data-type="radio" style="padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; cursor: grab; background: rgba(255,255,255,0.02); display: flex; align-items: center; gap: 10px; font-size: 0.85rem;" title="Clique ou arraste para adicionar">
                                <span>🔘</span> <strong><?php echo Helpers::__('lbl_tb_radio'); ?></strong>
                            </div>
                            <div class="toolbox-item" draggable="true" data-type="checkbox" style="padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; cursor: grab; background: rgba(255,255,255,0.02); display: flex; align-items: center; gap: 10px; font-size: 0.85rem;" title="Clique ou arraste para adicionar">
                                <span>☑</span> <strong><?php echo Helpers::__('lbl_tb_checkbox'); ?></strong>
                            </div>
                            <div class="toolbox-item" draggable="true" data-type="file" style="padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; cursor: grab; background: rgba(255,255,255,0.02); display: flex; align-items: center; gap: 10px; font-size: 0.85rem;" title="Clique ou arraste para adicionar">
                                <span>📎</span> <strong><?php echo Helpers::__('lbl_tb_file'); ?></strong>
                            </div>
                        </div>
                        
                        <div style="margin-top: 15px; font-size: 0.8rem; color: var(--text-secondary); line-height: 1.4; border: 1px dashed var(--border-color); padding: 10px; border-radius: 6px;">
                            <?php echo Helpers::__('lbl_form_builder_tip'); ?>
                        </div>
                    </div>
                    
                    <!-- Estrutura do Formulário -->
                    <div>
                        <h4 style="margin-bottom: 12px; font-size: 0.9rem; color: var(--accent-color);"><?php echo Helpers::__('lbl_form_structure'); ?></h4>
                        <div id="builder-canvas" style="min-height: 380px; border: 2px dashed var(--border-color); border-radius: 8px; padding: 15px; display: flex; flex-direction: column; gap: 10px; background: rgba(255,255,255,0.01); max-height: 500px; overflow-y: auto;">
                            <!-- Injetado via JS -->
                        </div>
                    </div>
                </div>
                
                <div style="text-align: right; margin-top: 25px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                    <button type="submit" class="btn btn-primary" onclick="saveFormBuilderData(event)"><?php echo Helpers::__('lbl_btn_save_form'); ?></button>
                </div>
            </form>
        </div>

        <!-- ABA 9: NOTIFICAÇÕES -->
        <div id="tab-notifications" class="tab-content" style="display: none;">
            <div class="card">
                <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;"><?php echo Helpers::__('lbl_notifications_title'); ?></h3>
                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:20px;"><?php echo Helpers::__('lbl_notifications_desc'); ?></p>
                
                <!-- Adicionar Regra -->
                <form action="index.php?route=tech/settings&action=notifications-add" method="post" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 25px; background: rgba(255,255,255,0.01);">
                    <h4 style="margin-bottom: 15px; font-size: 0.95rem; color: var(--accent-color);"><?php echo Helpers::__('lbl_add_notification_rule'); ?></h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; align-items: flex-end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label"><?php echo Helpers::__('lbl_notif_event'); ?></label>
                            <select name="event" class="form-control" required>
                                <option value="repair_request"><?php echo Helpers::__('lbl_event_repair_request'); ?></option>
                                <option value="chat_message"><?php echo Helpers::__('lbl_event_chat_message'); ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label"><?php echo Helpers::__('lbl_notif_target_type'); ?></label>
                            <select name="target_type" id="notif_target_type" class="form-control" required onchange="toggleNotificationTarget(this.value)">
                                <option value="email"><?php echo Helpers::__('lbl_target_email_custom'); ?></option>
                                <option value="user"><?php echo Helpers::__('lbl_target_user_tech'); ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <div id="notif_target_email_wrapper">
                                <label class="form-label"><?php echo Helpers::__('lbl_notif_email_address'); ?></label>
                                <input type="email" name="target_email" class="form-control" placeholder="exemplo@empresa.com">
                            </div>
                            
                            <div id="notif_target_user_wrapper" style="display: none;">
                                <label class="form-label"><?php echo Helpers::__('lbl_notif_select_user'); ?></label>
                                <select name="target_user" class="form-control">
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['username']); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div style="text-align: right; margin-top: 15px;">
                        <button type="submit" class="btn btn-primary" style="padding: 8px 20px; font-size: 0.85rem;"><?php echo Helpers::__('lbl_btn_add_rule'); ?></button>
                    </div>
                </form>
                
                <!-- Listagem de Regras -->
                <h4 style="margin-bottom: 12px; font-size: 0.95rem; color: var(--accent-color);"><?php echo Helpers::__('lbl_active_rules'); ?></h4>
                <?php 
                $notificationRules = json_decode(Helpers::getSetting('notification_rules', '[]'), true) ?: [];
                if (empty($notificationRules)): 
                ?>
                    <p style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;"><?php echo Helpers::__('lbl_active_rules_empty'); ?></p>
                <?php else: ?>
                    <table class="custom-table" style="font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th><?php echo Helpers::__('lbl_notif_event'); ?></th>
                                <th><?php echo Helpers::__('lbl_notif_target_type'); ?></th>
                                <th><?php echo Helpers::__('lbl_notif_detail'); ?></th>
                                <th style="text-align: right; width: 100px;"><?php echo Helpers::__('lbl_notif_actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notificationRules as $rule): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?php 
                                            if (($rule['event'] ?? '') === 'repair_request') {
                                                echo Helpers::__('lbl_event_repair_request');
                                            } elseif (($rule['event'] ?? '') === 'chat_message') {
                                                echo Helpers::__('lbl_event_chat_message');
                                            } else {
                                                echo htmlspecialchars($rule['event'] ?? '');
                                            }
                                            ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <?php echo ($rule['target_type'] ?? '') === 'email' ? Helpers::__('lbl_notif_email_type') : Helpers::__('lbl_notif_user_type'); ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (($rule['target_type'] ?? '') === 'email') {
                                            echo htmlspecialchars($rule['target_value'] ?? '');
                                        } else {
                                            $uName = 'N/A';
                                            foreach ($users as $u) {
                                                if ((int)$u['id'] === (int)($rule['target_value'] ?? 0)) {
                                                    $uName = $u['name'] . " (" . $u['username'] . ")";
                                                    break;
                                                }
                                            }
                                            echo htmlspecialchars($uName);
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <form action="index.php?route=tech/settings&action=notifications-delete" method="post" style="display: inline;">
                                            <input type="hidden" name="rule_id" value="<?php echo htmlspecialchars($rule['id'] ?? ''); ?>">
                                            <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem; background-color: rgba(239,68,68,0.1); color: var(--color-error); border: 1px solid rgba(239,68,68,0.2);"><?php echo Helpers::__('lbl_btn_remove'); ?></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    <!-- ABA 12: Cópias de Segurança (Backups & Restauro) -->
    <div id="tab-backups" class="tab-content" style="display: none;">
        <!-- Card 1: Criar Backup -->
        <div class="card" style="padding: 20px; margin-bottom: 30px;">
            <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 15px; color: var(--accent-color);">
                <?php echo Helpers::__('lbl_backup_create'); ?>
            </h3>
            <form action="index.php?route=tech/settings&action=backup-manual" method="post" style="display: flex; flex-direction: column; gap: 15px;">
                <div class="form-group">
                    <label class="form-label" for="backup_folder"><?php echo Helpers::__('lbl_backup_folder'); ?></label>
                    <input type="text" name="backup_folder" id="backup_folder" class="form-control" placeholder="<?php echo htmlspecialchars(Helpers::__('lbl_backup_folder_ph')); ?>">
                </div>
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary"><?php echo Helpers::__('btn_create_backup'); ?></button>
                </div>
            </form>
        </div>

        <!-- Card 2: Restaurar Backup -->
        <div class="card" style="padding: 20px; margin-bottom: 30px;">
            <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 15px; color: var(--accent-color);">
                <?php echo Helpers::__('lbl_backup_restore'); ?>
            </h3>
            <form action="index.php?route=tech/settings&action=backup-restore" method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
                <div class="form-group">
                    <label class="form-label"><?php echo Helpers::__('lbl_backup_file'); ?></label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="file" name="backup_file" id="backup_file" style="display: none;" accept=".sql,.sqlite,.db" onchange="updateFileName(this, 'backup-file-name')" required>
                        <label for="backup_file" class="btn btn-secondary" style="padding: 10px 16px; font-size: 0.85rem; height: 38px; cursor: pointer; margin-bottom: 0;">
                            📁 <?php echo Helpers::__('btn_choose_file'); ?>
                        </label>
                        <span id="backup-file-name" style="font-size: 0.85rem; color: var(--text-secondary);"><?php echo Helpers::__('lbl_no_file_chosen'); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?php echo Helpers::__('lbl_restore_mode'); ?></label>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 5px;">
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="restore_mode" value="full" checked style="width: 16px; height: 16px;">
                            <span><?php echo Helpers::__('opt_restore_full'); ?></span>
                        </label>
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="restore_mode" value="update" style="width: 16px; height: 16px;">
                            <span><?php echo Helpers::__('opt_restore_update'); ?></span>
                        </label>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Tem a certeza que pretende restaurar esta cópia de segurança? Esta operação pode alterar dados importantes.');"><?php echo Helpers::__('btn_restore_backup'); ?></button>
                </div>
            </form>
        </div>

        <!-- Card 3: Backup Automático (CRON) -->
        <div class="card" style="padding: 20px;">
            <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 15px; color: var(--accent-color);">
                <?php echo Helpers::__('lbl_backup_cron'); ?>
            </h3>
            <form action="index.php?route=tech/settings&action=backup-cron-save" method="post" style="display: flex; flex-direction: column; gap: 15px;">
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="backup_cron_enabled" id="backup_cron_enabled" value="1" <?php echo ($settings['backup_cron_enabled'] ?? '0') === '1' ? 'checked' : ''; ?> style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="backup_cron_enabled" class="form-label" style="margin-bottom: 0; cursor: pointer; font-weight: 600;">
                        <?php echo Helpers::__('lbl_cron_enable'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="backup_cron_frequency"><?php echo Helpers::__('lbl_cron_frequency'); ?></label>
                    <select name="backup_cron_frequency" id="backup_cron_frequency" class="form-control">
                        <option value="daily" <?php echo ($settings['backup_cron_frequency'] ?? 'daily') === 'daily' ? 'selected' : ''; ?>><?php echo Helpers::__('opt_cron_daily'); ?></option>
                        <option value="weekly" <?php echo ($settings['backup_cron_frequency'] ?? 'weekly') === 'weekly' ? 'selected' : ''; ?>><?php echo Helpers::__('opt_cron_weekly'); ?></option>
                        <option value="monthly" <?php echo ($settings['backup_cron_frequency'] ?? 'monthly') === 'monthly' ? 'selected' : ''; ?>><?php echo Helpers::__('opt_cron_monthly'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="backup_cron_folder"><?php echo Helpers::__('lbl_backup_folder'); ?></label>
                    <input type="text" name="backup_cron_folder" id="backup_cron_folder" class="form-control" value="<?php echo htmlspecialchars($settings['backup_cron_folder'] ?? ''); ?>" placeholder="<?php echo htmlspecialchars(Helpers::__('lbl_backup_folder_ph')); ?>">
                </div>

                <?php if (!empty($settings['backup_cron_token'])): ?>
                    <div class="form-group" style="margin-top: 10px; border-left: 2px solid var(--accent-color); padding-left: 15px;">
                        <label class="form-label" style="font-weight: 600; color: var(--accent-color);"><?php echo Helpers::__('lbl_cron_url'); ?></label>
                        <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                            <input type="text" class="form-control" readonly style="flex: 1; font-family: monospace; font-size: 0.85rem;" value="<?php 
                                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                                $host = $_SERVER['HTTP_HOST'];
                                $uri = dirname($_SERVER['REQUEST_URI']);
                                echo htmlspecialchars("{$protocol}://{$host}{$uri}/index.php?route=cron/backup&token={$settings['backup_cron_token']}");
                            ?>">
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 4px;">
                            <?php echo Helpers::__('lbl_cron_url_desc'); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="submit" class="btn btn-primary"><?php echo Helpers::__('btn_save_settings'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- ABA 13: Respostas Pré-definidas -->
    <div id="tab-predefined-responses" class="tab-content" style="display: none;">
        <div class="card" style="padding: 20px; margin-bottom: 30px;">
            <h3 style="font-size: 1.15rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;">
                <?php echo Helpers::__('lbl_predefined_responses_title'); ?>
            </h3>
            <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:20px;">
                <?php echo Helpers::__('lbl_predefined_responses_desc'); ?>
            </p>

            <!-- Listagem de Respostas Existentes -->
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 15px; margin-bottom: 25px; padding-left: 0;" id="predefined-responses-list">
                <?php foreach ($predefinedResponses as $resp): ?>
                    <li style="display:flex; flex-direction:column; background-color: var(--bg-card); border: 1px solid var(--border-color); padding: 15px; border-radius: var(--radius-sm); gap: 10px;" id="response-row-<?php echo $resp['id']; ?>">
                        <!-- Modo Visualização -->
                        <div class="response-view" style="display:flex; justify-content:space-between; align-items:flex-start; width:100%;">
                            <div style="flex:1;">
                                <strong style="color: var(--accent-color); font-size: 0.95rem;"><?php echo htmlspecialchars($resp['title']); ?></strong>
                                <p style="margin: 5px 0 0 0; font-size: 0.88rem; color: var(--text-secondary); white-space: pre-wrap;"><?php echo htmlspecialchars($resp['message']); ?></p>
                            </div>
                            <div style="display:flex; gap:6px; margin-left:15px;">
                                <button type="button" onclick="editResponse(<?php echo $resp['id']; ?>)" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.75rem;" title="Editar">✏️</button>
                                <form action="index.php?route=tech/settings&action=predefined-response-delete" method="post" onsubmit="return confirm('<?php echo htmlspecialchars(Helpers::__('msg_confirm_delete_response'), ENT_QUOTES); ?>');" style="margin:0;">
                                    <input type="hidden" name="response_id" value="<?php echo $resp['id']; ?>">
                                    <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem; background-color: var(--color-error-bg); color: var(--color-error); border-color:transparent;" title="Eliminar">❌</button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Modo Edição -->
                        <form class="response-edit" action="index.php?route=tech/settings&action=predefined-response-edit" method="post" style="display:none; flex-direction:column; gap:10px; width:100%;">
                            <input type="hidden" name="response_id" value="<?php echo $resp['id']; ?>">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" style="font-size:0.8rem;"><?php echo Helpers::__('lbl_response_title'); ?></label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($resp['title']); ?>" required style="font-size:0.9rem;">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" style="font-size:0.8rem;"><?php echo Helpers::__('lbl_response_message'); ?></label>
                                <textarea name="message" class="form-control" rows="3" required style="font-size:0.88rem;"><?php echo htmlspecialchars($resp['message']); ?></textarea>
                            </div>
                            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:5px;">
                                <button type="submit" class="btn btn-primary" style="padding:6px 15px; font-size:0.8rem;"><?php echo Helpers::__('btn_save_response'); ?></button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEditResponse(<?php echo $resp['id']; ?>)" style="padding:6px 12px; font-size:0.8rem;"><?php echo Helpers::__('Cancelar'); ?></button>
                            </div>
                        </form>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($predefinedResponses)): ?>
                    <li style="color: var(--text-muted); font-size:0.9rem; padding: 15px; text-align:center; border: 1px dashed var(--border-color); border-radius: var(--radius-sm);">
                        <?php echo Helpers::__('lbl_no_predefined_responses'); ?>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Criar Nova Resposta -->
            <form action="index.php?route=tech/settings&action=predefined-response-add" method="post" style="border-top: 1px solid var(--border-color); padding-top: 20px; display: flex; flex-direction: column; gap: 15px;">
                <h4 style="font-size: 1rem; color: var(--accent-color); margin-bottom: 5px;"><?php echo Helpers::__('lbl_add_predefined_response'); ?></h4>
                <div class="form-group">
                    <label class="form-label" for="new_response_title"><?php echo Helpers::__('lbl_response_title'); ?> *</label>
                    <input type="text" name="title" id="new_response_title" class="form-control" required placeholder="<?php echo htmlspecialchars(Helpers::__('lbl_response_title_ph')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="new_response_message"><?php echo Helpers::__('lbl_response_message'); ?> *</label>
                    <textarea name="message" id="new_response_message" class="form-control" rows="3" required placeholder="<?php echo htmlspecialchars(Helpers::__('lbl_response_message_ph')); ?>"></textarea>
                </div>
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary"><?php echo Helpers::__('btn_add_response'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    function updateFileName(input, spanId) {
        var span = document.getElementById(spanId);
        if (input.files && input.files.length > 0) {
            span.textContent = input.files[0].name;
        } else {
            span.textContent = "<?php echo Helpers::__('lbl_no_file_chosen'); ?>";
        }
    }

    function editResponse(id) {
        var row = document.getElementById('response-row-' + id);
        row.querySelector('.response-view').style.display = 'none';
        row.querySelector('.response-edit').style.display = 'flex';
    }

    function cancelEditResponse(id) {
        var row = document.getElementById('response-row-' + id);
        row.querySelector('.response-view').style.display = 'flex';
        row.querySelector('.response-edit').style.display = 'none';
    }

    // Sistema simples de troca de abas (Tabs) persistente em SessionStorage
    function switchTab(tabId) {
        // Ocultar todos os conteúdos
        var contents = document.querySelectorAll('.tab-content');
        contents.forEach(function(el) {
            el.style.display = 'none';
        });

        // Remover classe ativa dos botões
        var buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(function(btn) {
            btn.classList.remove('active');
        });

        // Mostrar o conteúdo pretendido
        document.getElementById('tab-' + tabId).style.display = 'block';
        
        // Adicionar classe ativa no botão clicado
        document.getElementById('tab-btn-' + tabId).classList.add('active');

        // Persistir a aba ativa
        sessionStorage.setItem('settings_active_tab', tabId);
    }

    // Inicialização da aba ativa ao carregar
    window.addEventListener('DOMContentLoaded', (event) => {
        var activeTab = sessionStorage.getItem('settings_active_tab');
        if (activeTab && document.getElementById('tab-' + activeTab)) {
            switchTab(activeTab);
        }
        if (document.getElementById('widget_url')) {
            generateWidgetCode();
        }
    });

    function toggleSettingsSmtp() {
        var enabled = document.getElementById('set_smtp_enabled').checked;
        var fields = document.getElementById('settings_smtp_fields');
        fields.style.display = enabled ? 'block' : 'none';
    }

    function togglePermissionsDiv(select) {
        var wrapper = document.getElementById('permissions-selector-wrapper');
        if (select.value === 'admin') {
            wrapper.style.display = 'none';
        } else {
            wrapper.style.display = 'block';
        }
    }

    function editDtype(index) {
        var row = document.getElementById('dtype-row-' + index);
        row.querySelector('.dtype-view').style.display = 'none';
        var editForm = row.querySelector('.dtype-edit');
        editForm.style.display = 'flex';
        var actions = row.querySelector('.dtype-actions');
        if (actions) actions.style.display = 'none';
    }

    function cancelEditDtype(index) {
        var row = document.getElementById('dtype-row-' + index);
        row.querySelector('.dtype-view').style.display = 'flex';
        var editForm = row.querySelector('.dtype-edit');
        editForm.style.display = 'none';
        var actions = row.querySelector('.dtype-actions');
        if (actions) actions.style.display = 'flex';
    }

    // --- LOGICA DE GERACAO DO WIDGET DE PESQUISA EXTERNA ---
    document.addEventListener('DOMContentLoaded', () => {
        const colorPicker = document.getElementById('widget_accent_color');
        const colorText = document.getElementById('widget_accent_color_text');

        if (colorPicker && colorText) {
            colorPicker.addEventListener('input', function() {
                colorText.value = colorPicker.value;
                generateWidgetCode();
            });
            colorText.addEventListener('input', function() {
                if (/^#[0-9A-F]{6}$/i.test(colorText.value)) {
                    colorPicker.value = colorText.value;
                }
                generateWidgetCode();
            });
        }

        ['widget_url', 'widget_label', 'widget_placeholder', 'widget_btn_text', 'widget_target', 'widget_bg_style'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', generateWidgetCode);
                el.addEventListener('change', generateWidgetCode);
            }
        });
    });

    function generateWidgetCode() {
        const urlInput = document.getElementById('widget_url');
        if (!urlInput) return;

        const url = urlInput.value.trim();
        const label = document.getElementById('widget_label').value.trim();
        const placeholder = document.getElementById('widget_placeholder').value.trim();
        const btnText = document.getElementById('widget_btn_text').value.trim();
        const target = document.getElementById('widget_target').value;
        const bgStyle = document.getElementById('widget_bg_style').value;
        const accentColor = document.getElementById('widget_accent_color_text').value.trim() || '#6366f1';

        // Garantir que a URL termina com barra
        let formattedUrl = url;
        if (formattedUrl && !formattedUrl.endsWith('/')) {
            formattedUrl += '/';
        }

        // Definir estilos de fundo
        let containerStyle = '';
        let inputStyle = '';
        let labelStyle = '';

        if (bgStyle === 'glass') {
            containerStyle = `background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37); color: #f3f4f6;`;
            inputStyle = `background: rgba(17, 24, 39, 0.8); border: 1px solid rgba(255, 255, 255, 0.08); color: #f3f4f6;`;
            labelStyle = `color: #9ca3af;`;
        } else if (bgStyle === 'light') {
            containerStyle = `background: #ffffff; border: 1px solid rgba(15, 23, 42, 0.08); box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); color: #0f172a;`;
            inputStyle = `background: #ffffff; border: 1px solid rgba(15, 23, 42, 0.15); color: #0f172a;`;
            labelStyle = `color: #475569;`;
        } else { // transparent
            containerStyle = `background: transparent; border: none; box-shadow: none; color: inherit; padding: 0;`;
            inputStyle = `background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.15); color: inherit;`;
            labelStyle = `color: inherit; opacity: 0.8;`;
        }

        let paddingStyle = bgStyle !== 'transparent' ? 'padding: 24px; border-radius: 14px;' : '';

        const widgetHtml = 
`<!-- RMA Gest Search Widget -->
<div class="rmagest-search-widget" style="font-family: system-ui, -apple-system, sans-serif; max-width: 400px; width: 100%; box-sizing: border-box; ${paddingStyle} ${containerStyle}">
    <form action="${formattedUrl}index.php" method="get" target="${target}">
        <input type="hidden" name="route" value="client/rma-view">
        <div style="margin-bottom: 16px; box-sizing: border-box;">
            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 8px; ${labelStyle}">${label}</label>
            <div style="position: relative; display: flex; align-items: center; box-sizing: border-box;">
                <span style="position: absolute; left: 14px; color: #888; pointer-events: none; font-size: 1rem;">🔍</span>
                <input type="text" name="rma" placeholder="${placeholder}" required style="width: 100%; padding: 10px 14px 10px 40px; border-radius: 8px; font-size: 0.95rem; font-family: inherit; outline: none; transition: border-color 0.15s ease; box-sizing: border-box; ${inputStyle}" onfocus="this.style.borderColor='${accentColor}'" onblur="this.style.borderColor=''">
            </div>
        </div>
        <button type="submit" style="width: 100%; padding: 12px; border-radius: 8px; border: none; background: ${accentColor}; color: #ffffff; font-weight: 600; font-size: 0.95rem; font-family: inherit; cursor: pointer; transition: opacity 0.2s; box-shadow: 0 4px 12px ${accentColor}40; box-sizing: border-box;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            ${btnText}
        </button>
    </form>
</div>`;

        document.getElementById('widget_code_output').value = widgetHtml;
        document.getElementById('widget-preview-container').innerHTML = widgetHtml;
    }

    function copyWidgetCode() {
        const codeText = document.getElementById('widget_code_output');
        codeText.select();
        codeText.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(codeText.value).then(() => {
            const copyBtn = document.getElementById('btn-copy-widget-code');
            const oldText = copyBtn.innerHTML;
            copyBtn.innerHTML = '✓ <?php echo Helpers::__('msg_copied'); ?>';
            copyBtn.classList.remove('btn-secondary');
            copyBtn.classList.add('btn-primary');
            setTimeout(() => {
                copyBtn.innerHTML = oldText;
                copyBtn.classList.remove('btn-primary');
                copyBtn.classList.add('btn-secondary');
            }, 2000);
        }).catch(err => {
            alert('Erro ao copiar código. Por favor selecione a caixa e copie manualmente.');
        });
    }

    // --- FORM BUILDER JS ---
    let formFields = [];

    function initFormBuilder() {
        let savedJson = <?php echo json_encode(Helpers::getSetting('custom_form_json', '')); ?>;
        if (savedJson && savedJson.trim() !== '') {
            try {
                formFields = JSON.parse(savedJson);
            } catch(e) {
                console.error("Erro parsing form json", e);
                formFields = getDefaultFields();
            }
        } else {
            formFields = getDefaultFields();
        }
        renderCanvas();
    }

    function getDefaultFields() {
        return [
            { id: 'core_client_name', type: 'text', label: '<?php echo htmlspecialchars(Helpers::__('lbl_fb_client_name'), ENT_QUOTES); ?>', required: true, mapped_to: 'client_name', is_core: true },
            { id: 'core_client_email', type: 'email', label: '<?php echo htmlspecialchars(Helpers::__('lbl_fb_client_email'), ENT_QUOTES); ?>', required: true, mapped_to: 'client_email', is_core: true },
            { id: 'core_client_contact', type: 'tel', label: '<?php echo htmlspecialchars(Helpers::__('lbl_fb_client_contact'), ENT_QUOTES); ?>', required: true, mapped_to: 'client_contact', is_core: true },
            { id: 'core_client_address', type: 'textarea', label: '<?php echo htmlspecialchars(Helpers::__('lbl_fb_client_address'), ENT_QUOTES); ?>', required: true, mapped_to: 'client_address', is_core: true },
            { id: 'core_device_type', type: 'select', label: '<?php echo htmlspecialchars(Helpers::__('lbl_fb_device_type'), ENT_QUOTES); ?>', required: true, mapped_to: 'device_type', is_core: true },
            { id: 'core_serial_number', type: 'text', label: '<?php echo htmlspecialchars(Helpers::__('lbl_fb_serial_number'), ENT_QUOTES); ?>', required: false, mapped_to: 'serial_number', is_core: true },
            { id: 'core_device_condition', type: 'textarea', label: '<?php echo htmlspecialchars(Helpers::__('lbl_fb_device_condition'), ENT_QUOTES); ?>', required: true, mapped_to: 'device_condition', is_core: true }
        ];
    }

    function renderCanvas() {
        const canvas = document.getElementById('builder-canvas');
        if (!canvas) return;
        
        canvas.innerHTML = '';
        
        if (formFields.length === 0) {
            canvas.innerHTML = '<div style="text-align:center; color:var(--text-muted); padding:40px 0;"><?php echo htmlspecialchars(Helpers::__('lbl_canvas_empty'), ENT_QUOTES); ?></div>';
            return;
        }
        
        formFields.forEach((field, index) => {
            const card = document.createElement('div');
            card.className = 'field-card';
            card.style.cssText = 'padding:14px; margin-bottom:8px; display:flex; flex-direction:column; gap:8px; border:1px solid var(--border-color); border-radius: 8px; background:rgba(255,255,255,0.015); position:relative; cursor:default;';
            card.setAttribute('data-id', field.id);
            card.setAttribute('data-index', index);
            
            // Drag events for reordering
            card.setAttribute('draggable', 'true');
            card.addEventListener('dragstart', handleCanvasDragStart);
            card.addEventListener('dragover', handleCanvasDragOver);
            card.addEventListener('drop', handleCanvasDrop);
            
            let typeLabel = '';
            switch(field.type) {
                case 'text': typeLabel = '<?php echo htmlspecialchars(Helpers::__('lbl_tb_text'), ENT_QUOTES); ?>'; break;
                case 'textarea': typeLabel = '<?php echo htmlspecialchars(Helpers::__('lbl_tb_textarea'), ENT_QUOTES); ?>'; break;
                case 'select': typeLabel = '<?php echo htmlspecialchars(Helpers::__('lbl_tb_select'), ENT_QUOTES); ?>'; break;
                case 'radio': typeLabel = '<?php echo htmlspecialchars(Helpers::__('lbl_tb_radio'), ENT_QUOTES); ?>'; break;
                case 'checkbox': typeLabel = '<?php echo htmlspecialchars(Helpers::__('lbl_tb_checkbox'), ENT_QUOTES); ?>'; break;
                case 'file': typeLabel = '<?php echo htmlspecialchars(Helpers::__('lbl_tb_file'), ENT_QUOTES); ?>'; break;
                case 'email': typeLabel = 'Email'; break;
                case 'tel': typeLabel = '<?php echo htmlspecialchars(Helpers::__('lbl_tb_tel'), ENT_QUOTES); ?>'; break;
                default: typeLabel = field.type;
            }

            const isCore = !!field.is_core;
            const coreBadge = isCore 
                ? '<span style="font-size:0.7rem; font-weight:bold; background:rgba(99,102,241,0.15); color:#818cf8; padding:2px 8px; border-radius:10px;"><?php echo htmlspecialchars(Helpers::__('lbl_badge_core'), ENT_QUOTES); ?></span>'
                : '<span style="font-size:0.7rem; font-weight:bold; background:rgba(16,185,129,0.15); color:#34d399; padding:2px 8px; border-radius:10px;"><?php echo htmlspecialchars(Helpers::__('lbl_badge_custom'), ENT_QUOTES); ?></span>';
            
            let optionsInputHtml = '';
            if (['select', 'radio', 'checkbox'].includes(field.type)) {
                const placeholderText = field.mapped_to === 'device_type' ? '<?php echo htmlspecialchars(Helpers::__('ph_field_options_device_type'), ENT_QUOTES); ?>' : '<?php echo htmlspecialchars(Helpers::__('ph_field_options_default'), ENT_QUOTES); ?>';
                optionsInputHtml = `
                    <div style="margin-top:5px;">
                        <label style="font-size:0.75rem; font-weight:600; display:block; margin-bottom:4px;"><?php echo htmlspecialchars(Helpers::__('lbl_field_options'), ENT_QUOTES); ?></label>
                        <input type="text" class="form-control field-options" style="padding:6px 10px; font-size:0.82rem;" value="${field.options ? field.options.join(', ') : ''}" placeholder="${placeholderText}" oninput="updateFieldOptions(${index}, this.value)">
                    </div>
                `;
            }
            
            card.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:6px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="cursor:grab; font-size:1.1rem; color:var(--text-muted);">☰</span>
                        <strong style="font-size:0.85rem;">${typeLabel}</strong>
                        ${coreBadge}
                    </div>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <button type="button" class="btn" style="padding:2px 6px; font-size:0.8rem; background:transparent; border:none; color:var(--text-primary); cursor:pointer;" onclick="moveField(${index}, -1)" ${index === 0 ? 'disabled' : ''}>▲</button>
                        <button type="button" class="btn" style="padding:2px 6px; font-size:0.8rem; background:transparent; border:none; color:var(--text-primary); cursor:pointer;" onclick="moveField(${index}, 1)" ${index === formFields.length - 1 ? 'disabled' : ''}>▼</button>
                        
                        ${!isCore ? `
                            <button type="button" class="btn" style="padding:4px 8px; font-size:0.75rem; background-color:rgba(239,68,68,0.1); color:var(--color-error); border:1px solid rgba(239,68,68,0.2); border-radius:4px; cursor:pointer;" onclick="removeField(${index})"><?php echo htmlspecialchars(Helpers::__('lbl_btn_delete'), ENT_QUOTES); ?></button>
                        ` : `
                            <span style="font-size:0.8rem; color:var(--text-muted); padding: 4px;" title="<?php echo htmlspecialchars(Helpers::__('lbl_core_fields_locked'), ENT_QUOTES); ?>">🔒</span>
                        `}
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:0.75rem; font-weight:600; display:block; margin-bottom:4px;"><?php echo htmlspecialchars(Helpers::__('lbl_field_label'), ENT_QUOTES); ?></label>
                        <input type="text" class="form-control field-label" style="padding:6px 10px; font-size:0.85rem;" value="${field.label || ''}" required oninput="updateFieldLabel(${index}, this.value)">
                    </div>
                    <div style="display:flex; align-items:flex-end; padding-bottom:8px;">
                        <label style="display:inline-flex; align-items:center; gap:6px; font-size:0.82rem; cursor:pointer;">
                            <input type="checkbox" class="field-required" ${field.required ? 'checked' : ''} onchange="updateFieldRequired(${index}, this.checked)">
                            <span><?php echo htmlspecialchars(Helpers::__('lbl_field_required'), ENT_QUOTES); ?></span>
                        </label>
                    </div>
                </div>
                ${optionsInputHtml}
            `;
            
            canvas.appendChild(card);
        });
    }

    function handleCanvasDragStart(e) {
        e.dataTransfer.setData('text/plain', e.target.getAttribute('data-index'));
        e.dataTransfer.effectAllowed = 'move';
    }

    function handleCanvasDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }

    function handleCanvasDrop(e) {
        e.preventDefault();
        const sourceIndex = parseInt(e.dataTransfer.getData('text/plain'));
        
        let target = e.target;
        while (target && !target.classList.contains('field-card')) {
            target = target.parentElement;
        }
        if (!target) return;
        
        const targetIndex = parseInt(target.getAttribute('data-index'));
        if (isNaN(sourceIndex) || sourceIndex === targetIndex) return;
        
        const element = formFields.splice(sourceIndex, 1)[0];
        formFields.splice(targetIndex, 0, element);
        renderCanvas();
    }

    function addField(type) {
        const newId = 'custom_field_' + Math.random().toString(36).substr(2, 9);
        formFields.push({
            id: newId,
            type: type,
            label: 'Novo Campo ' + (formFields.length + 1),
            required: false
        });
        renderCanvas();
        
        const canvas = document.getElementById('builder-canvas');
        canvas.scrollTop = canvas.scrollHeight;
    }

    function removeField(index) {
        if (formFields[index] && formFields[index].is_core) {
            alert("<?php echo htmlspecialchars(Helpers::__('msg_core_fields_delete_err'), ENT_QUOTES); ?>");
            return;
        }
        formFields.splice(index, 1);
        renderCanvas();
    }

    function moveField(index, direction) {
        const targetIndex = index + direction;
        if (targetIndex < 0 || targetIndex >= formFields.length) return;
        
        const temp = formFields[index];
        formFields[index] = formFields[targetIndex];
        formFields[targetIndex] = temp;
        renderCanvas();
    }

    function updateFieldLabel(index, val) {
        if (formFields[index]) {
            formFields[index].label = val;
        }
    }

    function updateFieldRequired(index, val) {
        if (formFields[index]) {
            formFields[index].required = val;
        }
    }

    function updateFieldOptions(index, val) {
        if (formFields[index]) {
            formFields[index].options = val.split(',').map(s => s.trim()).filter(s => s.length > 0);
        }
    }

    function toggleFormBuilderDisplay(mode) {
        const container = document.getElementById('drag-drop-builder-container');
        if (mode === 'custom') {
            container.style.display = 'grid';
            renderCanvas();
        } else {
            container.style.display = 'none';
        }
    }

    function saveFormBuilderData(e) {
        const mode = document.querySelector('input[name="form_mode"]:checked').value;
        if (mode === 'default') {
            return;
        }
        const missingCore = getDefaultFields().filter(cf => !formFields.some(f => f.mapped_to === cf.mapped_to));
        if (missingCore.length > 0) {
            alert("<?php echo htmlspecialchars(Helpers::__('msg_core_fields_missing_err'), ENT_QUOTES); ?>" + missingCore.map(c => c.label).join(', ') + ").");
            e.preventDefault();
            return;
        }
        
        document.getElementById('custom_form_json_input').value = JSON.stringify(formFields);
    }

    // --- NOTIFICATION ACTIONS JS ---
    function toggleNotificationTarget(val) {
        const emailWrapper = document.getElementById('notif_target_email_wrapper');
        const userWrapper = document.getElementById('notif_target_user_wrapper');
        if (val === 'email') {
            emailWrapper.style.display = 'block';
            userWrapper.style.display = 'none';
        } else {
            emailWrapper.style.display = 'none';
            userWrapper.style.display = 'block';
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        initFormBuilder();
        
        const toolboxItems = document.querySelectorAll('.toolbox-item');
        toolboxItems.forEach(item => {
            item.addEventListener('click', () => {
                const mode = document.querySelector('input[name="form_mode"]:checked').value;
                if (mode === 'custom') {
                    addField(item.getAttribute('data-type'));
                } else {
                    alert("<?php echo htmlspecialchars(Helpers::__('msg_custom_mode_required'), ENT_QUOTES); ?>");
                }
            });
            
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('application/toolbox-type', item.getAttribute('data-type'));
                e.dataTransfer.effectAllowed = 'copy';
            });
        });
        
        const canvas = document.getElementById('builder-canvas');
        if (canvas) {
            canvas.addEventListener('dragover', (e) => {
                e.preventDefault();
            });
            
            canvas.addEventListener('drop', (e) => {
                e.preventDefault();
                const type = e.dataTransfer.getData('application/toolbox-type');
                if (type) {
                    addField(type);
                }
            });
        }
    });
</script>

