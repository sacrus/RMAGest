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
use RmaGest\Auth;
$pageTitle = Helpers::__('title_manage') . " " . htmlspecialchars($rma['rma_number']);

$statuses = Helpers::getStatuses();
$canDelete = Auth::checkPermission('delete_rmas');
$canForget = Auth::checkPermission('privacy_rgpd');
$hasClientData = !empty($rma['client_name']);
?>

<!-- Folha de Layout de Impressão Invisível no Ecrã -->
<div class="print-report-header" style="display: none;">
    <div>
        <h2><?php echo Helpers::__('print_tech_report'); ?></h2>
        <h1><?php echo htmlspecialchars($rma['rma_number']); ?></h1>
        <p><?php echo Helpers::__('print_workshop'); ?>: <?php echo htmlspecialchars($siteName); ?></p>
    </div>
    <?php if (!empty($logoUrl)): ?>
        <img src="<?php echo htmlspecialchars($logoUrl); ?>" class="print-logo" alt="Logo">
    <?php endif; ?>
</div>

<div class="no-print" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
    <a href="index.php?route=tech/dashboard" style="font-weight: 500; font-size: 0.9rem;">&larr; <?php echo Helpers::__('btn_back_dashboard'); ?></a>
    <div style="display: flex; gap: 10px;">
        <button onclick="window.print()" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.9rem;">
            <?php echo Helpers::__('btn_print_report'); ?>
        </button>
        
        <?php if ($canForget && $hasClientData): ?>
            <form action="index.php?route=tech/rma-forget" method="post" onsubmit="return confirm('ATENÇÃO: Isto apagará de forma permanente todos os dados pessoais do cliente (Nome, Morada, Contacto e Email) em conformidade com o RGPD. O histórico do equipamento e diagnóstico será mantido. Tem a certeza que deseja continuar?');" style="display: inline;">
                <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
                <button type="submit" class="btn btn-secondary" style="background-color: var(--color-error-bg); color: var(--color-error); border-color: rgba(239,68,68,0.2); padding: 8px 16px; font-size: 0.9rem;">
                    <?php echo Helpers::__('btn_forget_client'); ?>
                </button>
            </form>
        <?php endif; ?>

        <?php if ($canDelete): ?>
            <form action="index.php?route=tech/rma-delete" method="post" onsubmit="return confirm('TEM A CERTEZA? Esta ação irá eliminar permanentemente este RMA e todo o seu histórico de chat e componentes. Esta ação é irreversível.');" style="display: inline;">
                <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
                <button type="submit" class="btn btn-secondary" style="background-color: rgba(239,68,68,0.1); color: var(--color-error); border: 1px solid rgba(239,68,68,0.3); padding: 8px 16px; font-size: 0.9rem;">
                    <?php echo Helpers::__('btn_delete_rma'); ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2><?php echo Helpers::__('lbl_technical_file'); ?> <?php echo htmlspecialchars($rma['rma_number']); ?></h2>
    <div>
        <?php echo Helpers::getStatusBadge($rma['current_status']); ?>
    </div>
</div>

<!-- SECÇÃO DE IMPRESSÃO EXCLUSIVA -->
<div class="print-grid" style="display: none;">
    <div class="print-section">
        <h3><?php echo Helpers::__('lbl_client_data'); ?></h3>
        <p><strong>Nome:</strong> <?php echo htmlspecialchars($rma['client_name'] ?: Helpers::__('msg_gdpr_anonymized')); ?></p>
        <p><strong>Contacto:</strong> <?php echo htmlspecialchars($rma['client_contact'] ?: 'N/A'); ?> (<?php echo $rma['allow_sms_whatsapp'] == 1 ? Helpers::__('lbl_sms_whatsapp_authorized') : Helpers::__('lbl_sms_whatsapp_denied'); ?>)</p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($rma['client_email'] ?: 'N/A'); ?></p>
        <p><strong>Morada:</strong> <?php echo nl2br(htmlspecialchars($rma['client_address'] ?: 'N/A')); ?></p>
    </div>
    <div class="print-section">
        <h3><?php echo Helpers::__('lbl_device_fiche'); ?></h3>
        <p><strong>Aparelho:</strong> <?php echo htmlspecialchars(Helpers::__($rma['device_type'])); ?></p>
        <p><strong>Nº Série:</strong> <?php echo htmlspecialchars($rma['serial_number'] ?: 'N/A'); ?></p>
        <p><strong>Estado Físico:</strong> <?php echo htmlspecialchars($rma['device_condition']); ?></p>
        <p><strong>Data de Entrada:</strong> <?php echo date('d/m/Y H:i', strtotime($rma['created_at'])); ?></p>
    </div>
</div>

<!-- SECÇÃO DE VISUALIZAÇÃO NO ECRÃ -->
<div class="no-print" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px;" id="tech-detail-grid">
    <!-- Bloco Esquerdo: Atualização de Estado, Relatório Técnico, Peças e Componentes -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
        
        <!-- Ficha de Entrada & Dados do Aparelho -->
        <div class="card">
            <h3 style="font-size: 1.1rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;"><?php echo Helpers::__('view_sec_sheet'); ?></h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4 style="font-size:0.95rem; margin-bottom:5px;"><?php echo Helpers::__('sec_device'); ?></h4>
                    <p style="font-weight: 600;"><?php echo htmlspecialchars(Helpers::__($rma['device_type'])); ?></p>
                    <p style="font-size: 0.85rem; color: var(--text-secondary);"><?php echo Helpers::__('lbl_serial_num'); ?> <span style="font-family:monospace;"><?php echo htmlspecialchars($rma['serial_number'] ?: 'N/A'); ?></span></p>
                    <p style="margin-top: 10px; font-size: 0.9rem;"><strong><?php echo Helpers::__('lbl_physical_condition'); ?></strong> <?php echo nl2br(htmlspecialchars($rma['device_condition'])); ?></p>
                </div>
                
                <div>
                    <h4 style="font-size:0.95rem; margin-bottom:5px;"><?php echo Helpers::__('lbl_client_data'); ?></h4>
                    <?php if ($hasClientData): ?>
                        <p style="font-weight: 600;"><?php echo htmlspecialchars($rma['client_name']); ?></p>
                        <p style="font-size: 0.85rem; color: var(--text-secondary);">
                            📞 <?php echo htmlspecialchars($rma['client_contact']); ?>
                            <span style="font-size: 0.72rem; font-weight: bold; margin-left: 5px; color: <?php echo $rma['allow_sms_whatsapp'] == 1 ? 'var(--color-success)' : 'var(--color-error)'; ?>;">
                                (<?php echo $rma['allow_sms_whatsapp'] == 1 ? '✓ SMS/WA ok' : '✗ ' . Helpers::__('lbl_sms_whatsapp_denied'); ?>)
                            </span>
                        </p>
                        <p style="font-size: 0.85rem; color: var(--text-secondary);">📧 <?php echo htmlspecialchars($rma['client_email']); ?></p>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;"><?php echo nl2br(htmlspecialchars($rma['client_address'])); ?></p>
                    <?php else: ?>
                        <p style="color: var(--color-error); font-style: italic;"><?php echo Helpers::__('msg_gdpr_anonymized'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($rma['custom_form_data'])): 
            $customFields = json_decode($rma['custom_form_data'], true);
            if (is_array($customFields) && !empty($customFields)):
        ?>
            <!-- Respostas do Formulário Personalizado -->
            <div class="card">
                <h3 style="font-size: 1.1rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;">📋 Campos do Formulário Personalizado</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <?php foreach ($customFields as $field): ?>
                        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                            <span style="font-size: 0.82rem; color: var(--text-secondary); display: block; font-weight: 600;"><?php echo htmlspecialchars($field['label']); ?></span>
                            <span style="font-size: 0.95rem;">
                                <?php if (($field['type'] ?? '') === 'file' && !empty($field['value'])): ?>
                                    <a href="<?php echo htmlspecialchars($field['value']); ?>" target="_blank">📄 Ver Ficheiro Anexo</a>
                                <?php else: ?>
                                    <?php echo !empty($field['value']) ? nl2br(htmlspecialchars($field['value'])) : '<span style="color:var(--text-muted); font-style:italic;">Não preenchido</span>'; ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; endif; ?>

        <!-- Formulário 1: Alteração de Estado e Relatório Técnico -->
        <form action="index.php?route=tech/rma-update" method="post" class="card">
            <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
            
            <h3 style="font-size: 1.1rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;"><?php echo Helpers::__('lbl_update_repair'); ?></h3>
            
            <div class="form-group">
                <label class="form-label" for="current_status"><?php echo Helpers::__('lbl_change_rma_status'); ?></label>
                <select name="current_status" id="current_status" class="form-control">
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?php echo htmlspecialchars($status); ?>" <?php echo $rma['current_status'] === $status ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(Helpers::__($status)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 4px;"><?php echo Helpers::__('lbl_change_rma_status_desc'); ?></p>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <input type="checkbox" name="allow_sms_whatsapp" id="allow_sms_whatsapp" value="1" <?php echo $rma['allow_sms_whatsapp'] == 1 ? 'checked' : ''; ?> style="width: 18px; height: 18px; cursor: pointer;">
                <label for="allow_sms_whatsapp" class="form-label" style="margin-bottom:0; cursor: pointer; font-weight: 600;">
                    <?php echo Helpers::__('lbl_allow_notifications'); ?>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="status_comment"><?php echo Helpers::__('lbl_public_comment'); ?></label>
                <textarea name="status_comment" id="status_comment" class="form-control" rows="2" placeholder="<?php echo htmlspecialchars(Helpers::__('ph_public_comment')); ?>"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="tech_report"><?php echo Helpers::__('lbl_tech_report_private'); ?></label>
                <textarea name="tech_report" id="tech_report" class="form-control" rows="4" placeholder="<?php echo htmlspecialchars(Helpers::__('ph_tech_report_private')); ?>"><?php echo htmlspecialchars($rma['tech_report'] ?? ''); ?></textarea>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" id="btn-save-diagnostics"><?php echo Helpers::__('btn_save_diagnostic'); ?></button>
            </div>
        </form>

        <!-- Formulário 2: Gestão de Peças e Componentes Utilizados -->
        <div class="card">
            <h3 style="font-size: 1.1rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;"><?php echo Helpers::__('lbl_parts_used'); ?></h3>
            
            <!-- Listagem de peças já adicionadas -->
            <div style="margin-bottom: 20px;">
                <?php if (empty($rmaComponents)): ?>
                    <p style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;"><?php echo Helpers::__('lbl_no_parts_associated'); ?></p>
                <?php else: ?>
                    <table class="custom-table" style="font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th><?php echo Helpers::__('th_component'); ?></th>
                                <th><?php echo Helpers::__('th_qty'); ?></th>
                                <th><?php echo Helpers::__('th_unit_price'); ?></th>
                                <th><?php echo Helpers::__('th_total_price'); ?></th>
                                <th style="text-align: right;"><?php echo Helpers::__('th_actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalComponents = 0;
                            foreach ($rmaComponents as $comp): 
                                $subtotal = $comp['quantity'] * $comp['price_per_unit'];
                                $totalComponents += $subtotal;
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($comp['component_name']); ?></strong>
                                        <?php if (!empty($comp['stock_id'])): ?>
                                            <span style="font-size: 0.72rem; color: var(--color-success); display: block;"><?php echo Helpers::__('lbl_stock_withdrawn'); ?></span>
                                        <?php else: ?>
                                            <span style="font-size: 0.72rem; color: var(--text-muted); display: block;"><?php echo Helpers::__('lbl_manual_input'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $comp['quantity']; ?></td>
                                    <td><?php echo number_format($comp['price_per_unit'], 2, ',', ' '); ?> €</td>
                                    <td><strong><?php echo number_format($subtotal, 2, ',', ' '); ?> €</strong></td>
                                    <td style="text-align: right;">
                                        <form action="index.php?route=tech/rma-delete-component" method="post" style="display:inline;">
                                            <input type="hidden" name="component_id" value="<?php echo $comp['id']; ?>">
                                            <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
                                            <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem; background-color: var(--color-error-bg); color: var(--color-error); border-color:transparent;" title="Remover Componente">❌</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr style="background-color: rgba(255,255,255,0.02); font-weight:700;">
                                <td colspan="3" style="text-align: right; padding: 12px 16px;"><?php echo Helpers::__('lbl_total_components'); ?></td>
                                <td colspan="2" style="color: var(--accent-color); font-size:1.05rem; padding: 12px 16px;">
                                    <?php echo number_format($totalComponents, 2, ',', ' '); ?> €
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Adicionar nova peça -->
            <h4 style="font-size: 0.95rem; margin-bottom: 12px;"><?php echo Helpers::__('lbl_associate_component'); ?></h4>
            <form action="index.php?route=tech/rma-add-component" method="post" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 10px; align-items: end;">
                <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size:0.75rem; margin-bottom: 4px;"><?php echo Helpers::__('lbl_part_select'); ?></label>
                    <select name="stock_id" id="stock_item_select" class="form-control" style="font-size: 0.85rem; padding: 10px;" onchange="onStockSelectChange(this)">
                        <option value=""><?php echo Helpers::__('lbl_manual_input'); ?></option>
                        <?php foreach ($stockItems as $item): ?>
                            <option value="<?php echo $item['id']; ?>" data-name="<?php echo htmlspecialchars($item['name']); ?>" data-price="<?php echo $item['price']; ?>" data-qty="<?php echo $item['quantity']; ?>">
                                <?php echo htmlspecialchars($item['name']); ?> (Disponível: <?php echo $item['quantity']; ?> | <?php echo number_format($item['price'], 2, ',', ' '); ?>€)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;" id="manual_name_group">
                    <label class="form-label" style="font-size:0.75rem; margin-bottom: 4px;"><?php echo Helpers::__('lbl_manual_part_name'); ?></label>
                    <input type="text" name="manual_name" id="manual_name" class="form-control" style="font-size: 0.85rem; padding: 10px;" placeholder="<?php echo htmlspecialchars(Helpers::__('ph_manual_part_name')); ?>">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size:0.75rem; margin-bottom: 4px;"><?php echo Helpers::__('th_qty'); ?></label>
                    <input type="number" name="quantity" id="comp_qty" class="form-control" style="font-size: 0.85rem; padding: 10px;" value="1" min="1" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size:0.75rem; margin-bottom: 4px;"><?php echo Helpers::__('th_unit_price'); ?> (€)</label>
                    <input type="number" step="0.01" name="price_per_unit" id="comp_price" class="form-control" style="font-size: 0.85rem; padding: 10px;" placeholder="0,00" required>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 10px; font-size: 0.85rem; width: 100%; height:42px;"><?php echo Helpers::__('btn_add_part'); ?></button>
            </form>
        </div>
    </div>

    <!-- Bloco Direito: Gestão de Orçamento & Chat de Comunicação -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
        
        <!-- Cartão de Orçamento -->
        <form action="index.php?route=tech/rma-budget" method="post" class="card">
            <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
            <h3 style="font-size: 1.1rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;"><?php echo Helpers::__('view_sec_budget'); ?></h3>
            
            <div class="form-group">
                <label class="form-label" for="budget_amount"><?php echo Helpers::__('lbl_budget_total'); ?></label>
                <input type="number" step="0.01" name="budget_amount" id="budget_amount" class="form-control" value="<?php echo htmlspecialchars($rma['budget_amount']); ?>" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label class="form-label" for="budget_paid"><?php echo Helpers::__('lbl_payment_status'); ?></label>
                <select name="budget_paid" id="budget_paid" class="form-control">
                    <option value="0" <?php echo $rma['budget_paid'] == 0 ? 'selected' : ''; ?>>⏳ <?php echo Helpers::__('lbl_pending'); ?></option>
                    <option value="1" <?php echo $rma['budget_paid'] == 1 ? 'selected' : ''; ?>>✓ <?php echo Helpers::__('lbl_paid'); ?></option>
                </select>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 0.85rem;" id="btn-save-budget"><?php echo Helpers::__('btn_save_budget'); ?></button>
            </div>
        </form>

        <!-- Código de Acesso do Cliente -->
        <div class="card">
            <h3 style="font-size: 1.1rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;"><?php echo Helpers::__('lbl_client_access'); ?></h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 10px;"><?php echo Helpers::__('lbl_client_access_desc'); ?></p>
            <div style="background-color: var(--bg-main); border:1px solid var(--border-color); border-radius: var(--radius-sm); padding:12px; text-align:center; font-family: monospace; font-size: 1.5rem; font-weight:800; letter-spacing: 2px; color: var(--color-success);">
                <?php echo htmlspecialchars($rma['access_code']); ?>
            </div>
            <p style="font-size:0.75rem; color: var(--text-muted); margin-top:8px;"><?php echo Helpers::__('lbl_client_access_disclaimer'); ?></p>
        </div>

        <!-- Chat com o Cliente -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="chat-section" style="border: none; border-radius: 0; height: 350px;">
                <div class="chat-header">
                    <strong><?php echo Helpers::__('lbl_chat_with_client'); ?></strong>
                    <span class="badge badge-info">Técnico</span>
                </div>
                
                <div class="chat-messages" id="tech-chat-box">
                    <?php if (empty($chatMessages)): ?>
                        <div class="chat-bubble system">
                            <?php echo Helpers::__('lbl_chat_empty_tech'); ?>
                        </div>
                    <?php else: ?>
                        <?php 
                        foreach ($chatMessages as $msg): 
                            $senderType = $msg['sender_type'];
                            $class = $senderType;
                            if ($senderType === 'client') {
                                $class = 'client';
                            } elseif ($senderType === 'tech') {
                                $class = 'tech';
                            } else {
                                $class = 'system';
                            }
                        ?>
                            <div class="chat-bubble <?php echo $class; ?>" style="<?php echo $senderType === 'tech' ? 'align-self: flex-end; background-color: var(--accent-color); color:#fff; border-bottom-right-radius:4px; border-bottom-left-radius:var(--radius-md); border:none;' : ($senderType === 'client' ? 'align-self: flex-start; background-color: var(--bg-card); border-bottom-left-radius:4px; border-bottom-right-radius:var(--radius-md);' : ''); ?>">
                                <?php if ($senderType !== 'system'): ?>
                                    <div style="font-size: 0.7rem; opacity: 0.8; font-weight: 700; margin-bottom: 2px;">
                                        <?php echo htmlspecialchars($msg['sender_name']); ?>
                                    </div>
                                <?php endif; ?>

                                <div style="font-size: 0.88rem; line-height: 1.4;">
                                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </div>

                                <?php if (!empty($msg['file_path'])): ?>
                                    <div style="margin-top: 8px; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.15); font-size: 0.78rem;">
                                        📎 <a href="<?php echo htmlspecialchars($msg['file_path']); ?>" target="_blank" style="color: inherit; text-decoration: underline;">
                                            <?php echo Helpers::__('chat_view_attachment'); ?> (<?php echo basename($msg['file_path']); ?>)
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="chat-meta" style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: 4px; min-width: 120px;">
                                    <span><?php echo date('d/m H:i', strtotime($msg['created_at'])); ?></span>
                                    <form action="index.php?route=tech/chat-delete" method="post" onsubmit="return confirm('<?php echo htmlspecialchars(Helpers::__('msg_confirm_delete_chat'), ENT_QUOTES); ?>');" style="margin: 0; display: inline-flex; align-items: center;">
                                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                        <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
                                        <button type="submit" style="background: none; border: none; padding: 0; color: <?php echo $senderType === 'tech' ? 'rgba(255,255,255,0.7)' : 'rgba(239, 68, 68, 0.7)'; ?>; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center;" title="<?php echo htmlspecialchars(Helpers::__('btn_delete_msg'), ENT_QUOTES); ?>">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="chat-input-area" style="padding: 10px;">
                    <!-- Menu Dropdown de Respostas Rápidas -->
                    <div style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                        <label for="predefined_response_select" style="font-size: 0.78rem; color: var(--text-secondary); margin-bottom: 0; white-space: nowrap;">
                            💡 <?php echo Helpers::__('lbl_predefined_responses'); ?>:
                        </label>
                        <select id="predefined_response_select" class="form-control" style="font-size: 0.8rem; padding: 4px 8px; height: 28px; flex: 1; background-color: var(--bg-surface-solid); border-color: var(--border-color); color: var(--text-main); font-weight: normal;" onchange="insertPredefinedResponse(this)">
                            <option value="" style="background-color: var(--bg-surface-solid); color: var(--text-main);"><?php echo Helpers::__('opt_select_predefined_response'); ?></option>
                            <?php foreach ($predefinedResponses as $resp): ?>
                                <option value="<?php echo htmlspecialchars($resp['message']); ?>" style="background-color: var(--bg-surface-solid); color: var(--text-main);">
                                    <?php echo htmlspecialchars($resp['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <form action="index.php?route=tech/chat-send" method="post" enctype="multipart/form-data" class="chat-form">
                        <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
                        <textarea name="message" id="tech_chat_message" placeholder="<?php echo htmlspecialchars(Helpers::__('ph_msg_for_client')); ?>" required autocomplete="off" class="form-control" style="flex: 1; font-size: 0.85rem; padding: 8px 12px; height: 38px; resize: vertical; min-height: 38px;"></textarea>
                        
                        <label for="tech_chat_attachment" class="btn btn-secondary" style="padding: 8px 12px; cursor: pointer;" title="Anexar Foto/Ficheiro">
                            📎
                            <input type="file" name="chat_attachment" id="tech_chat_attachment" style="display:none;" onchange="updateTechAttachLabel(this)">
                        </label>
                        <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.85rem;"><?php echo Helpers::__('btn_send'); ?></button>
                    </form>
                    <div id="tech-file-selected" style="font-size: 0.7rem; color: var(--color-success); margin-top: 5px; display:none;"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- SECÇÃO DE IMPRESSÃO EXCLUSIVA DO HISTÓRICO DE OCORRÊNCIAS -->
<div class="print-timeline" style="display: none;">
    <h2>HISTÓRICO COMPLETO DE OCORRÊNCIAS (CHAT)</h2>
    <div style="margin-top: 20px;">
        <?php foreach ($chatMessages as $msg): ?>
            <div class="print-timeline-item">
                <p style="font-size: 10pt; color: #555555;">
                    <strong>[<?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>] <?php echo htmlspecialchars($msg['sender_name']); ?></strong>
                    (<?php echo $msg['sender_type'] === 'tech' ? 'Técnico' : ($msg['sender_type'] === 'client' ? 'Cliente' : 'Sistema'); ?>)
                </p>
                <p style="margin-top: 5px; white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></p>
                <?php if (!empty($msg['file_path'])): ?>
                    <p style="font-size: 9pt; color:#666; margin-top: 3px;">📎 <em>Anexo associado: <?php echo basename($msg['file_path']); ?></em></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // Fazer scroll automático para o final do chat
    window.onload = function() {
        var chatBox = document.getElementById('tech-chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    };

    function updateTechAttachLabel(input) {
        var info = document.getElementById('tech-file-selected');
        if (input.files && input.files.length > 0) {
            info.innerHTML = "<?php echo Helpers::__('chat_file_selected'); ?> <strong>" + input.files[0].name + "</strong>";
            info.style.display = "block";
        } else {
            info.style.display = "none";
        }
    }

    function onStockSelectChange(select) {
        var option = select.options[select.selectedIndex];
        var manualNameGroup = document.getElementById('manual_name_group');
        var manualNameInput = document.getElementById('manual_name');
        var compPriceInput = document.getElementById('comp_price');
        var compQtyInput = document.getElementById('comp_qty');

        if (select.value === "") {
            // Se for manual
            manualNameGroup.style.display = 'block';
            manualNameInput.required = true;
            compPriceInput.value = "";
            compQtyInput.max = "";
        } else {
            // Se for do stock
            manualNameGroup.style.display = 'none';
            manualNameInput.required = false;
            
            var price = option.getAttribute('data-price');
            var qty = option.getAttribute('data-qty');
            
            compPriceInput.value = price;
            compQtyInput.max = qty; // Evita selecionar mais do que o stock disponível
        }
    }

    function insertPredefinedResponse(select) {
        var val = select.value;
        if (val) {
            var textarea = document.getElementById('tech_chat_message');
            if (textarea) {
                textarea.value = val;
                textarea.focus();
            }
            select.value = "";
        }
    }
</script>

