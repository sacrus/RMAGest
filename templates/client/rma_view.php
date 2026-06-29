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
$pageTitle = Helpers::__('view_title') . " " . htmlspecialchars($rma['rma_number']);

$currentStatus = $rma['current_status'];

// Determinar se o cliente está autenticado para ver o chat deste RMA específico
$isAuthenticated = isset($_SESSION['rma_auth_' . $rma['id']]) && $_SESSION['rma_auth_' . $rma['id']] === true;
?>

<div style="margin-bottom: 24px;">
    <a href="index.php" style="font-weight: 500; font-size: 0.9rem;">&larr; <?php echo Helpers::__('new_rma_back'); ?></a>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
        <h2><?php echo Helpers::__('view_heading'); ?> <?php echo htmlspecialchars($rma['rma_number']); ?></h2>
        <div>
            <?php echo Helpers::getStatusBadge($rma['current_status']); ?>
        </div>
    </div>
    <p style="color: var(--text-secondary); font-size: 0.9rem;"><?php echo Helpers::__('view_registered_at'); ?> <?php echo date('d/m/Y H:i', strtotime($rma['created_at'])); ?></p>
</div>

<!-- Secção 1: Estado Atual da Reparação -->
<div class="card" style="margin-bottom: 30px;">
    <h3 style="font-size: 1.1rem; margin-bottom: 20px;"><?php echo Helpers::__('view_timeline_title'); ?></h3>
    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 200px;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-color), var(--accent-hover, #3730a3)); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(79,70,229,0.35);">
                🔧
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); font-weight: 600; margin-bottom: 4px;"><?php echo Helpers::__('view_timeline_title'); ?></div>
                <div style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); line-height: 1.2;"><?php echo htmlspecialchars(Helpers::__($currentStatus)); ?></div>
            </div>
        </div>
        <div style="flex-shrink: 0;">
            <?php echo Helpers::getStatusBadge($currentStatus); ?>
        </div>
    </div>
    <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--border-color); margin-bottom: 0;">
        <?php echo Helpers::__('view_registered_at'); ?> <?php echo date('d/m/Y H:i', strtotime($rma['created_at'])); ?> &nbsp;·&nbsp; <?php echo Helpers::__('view_lbl_category'); ?> <strong><?php echo htmlspecialchars(Helpers::__($rma['device_type'])); ?></strong>
    </p>
</div>

<!-- Secção 2: Grid Lateral (Detalhes do RMA & Orçamento) -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;" id="client-info-grid">
    
    <!-- Detalhes do Equipamento -->
    <div class="card">
        <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 16px; color: var(--accent-color);">
            <?php echo Helpers::__('view_sec_sheet'); ?>
        </h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                <td style="padding: 10px 0; color: var(--text-secondary); font-weight: 500; width: 40%;"><?php echo Helpers::__('view_lbl_category'); ?></td>
                <td style="padding: 10px 0; font-weight: 600;"><?php echo htmlspecialchars(Helpers::__($rma['device_type'])); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                <td style="padding: 10px 0; color: var(--text-secondary); font-weight: 500;"><?php echo Helpers::__('view_lbl_serial'); ?></td>
                <td style="padding: 10px 0; font-family: monospace;"><?php echo htmlspecialchars($rma['serial_number'] ?: Helpers::__('view_not_provided')); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px 0; color: var(--text-secondary); font-weight: 500; vertical-align: top;"><?php echo Helpers::__('view_lbl_symptoms'); ?></td>
                <td style="padding: 10px 0; line-height: 1.4;"><?php echo nl2br(htmlspecialchars($rma['device_condition'])); ?></td>
            </tr>
        </table>
    </div>

    <!-- Detalhes do Orçamento -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 16px; color: var(--accent-color);">
                <?php echo Helpers::__('view_sec_budget'); ?>
            </h3>
            
            <?php if ($rma['budget_amount'] > 0): ?>
                <div style="margin-bottom: 16px;">
                    <span style="font-size: 2.2rem; font-weight: 800; color: var(--text-main);"><?php echo number_format($rma['budget_amount'], 2, ',', ' '); ?> €</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span><?php echo Helpers::__('view_budget_status'); ?></span>
                    <?php if ($rma['budget_paid'] == 1): ?>
                        <span class="badge badge-success"><?php echo Helpers::__('view_budget_paid'); ?></span>
                    <?php else: ?>
                        <span class="badge badge-warning"><?php echo Helpers::__('view_budget_pending'); ?></span>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">
                    <?php echo Helpers::__('view_budget_wait'); ?>
                </p>
            <?php endif; ?>
        </div>
        
        <?php if ($rma['budget_amount'] > 0 && $rma['budget_paid'] == 0): ?>
            <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 15px; border-top: 1px solid var(--border-color); padding-top: 10px;">
                <?php echo Helpers::__('view_budget_disclaimer'); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- Secção 3: Chat Interativo Bloqueado / Desbloqueado -->
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 30px;">
    <?php if (!$isAuthenticated): ?>
        <!-- Bloqueio do Chat por Código -->
        <div style="padding: 40px; text-align: center; max-width: 500px; margin: 0 auto;">
            <div style="font-size: 3rem; margin-bottom: 16px;">🔒</div>
            <h3 style="margin-bottom: 12px;"><?php echo Helpers::__('chat_lock_title'); ?></h3>
            <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 24px;">
                <?php echo Helpers::__('chat_lock_desc'); ?>
            </p>
            
            <?php if (isset($_GET['error']) && $_GET['error'] === 'auth_failed'): ?>
                <div style="background-color: var(--color-error-bg); color: var(--color-error); border: 1px solid var(--color-error); padding: 8px 16px; border-radius: var(--radius-sm); margin-bottom: 15px; font-size: 0.9rem; font-weight:500;">
                    <?php echo Helpers::__('chat_lock_error'); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?route=client/rma-auth" method="post" style="display: flex; gap: 12px; justify-content: center;">
                <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
                <input type="text" name="access_code" placeholder="<?php echo Helpers::__('chat_lock_ph'); ?>" required maxlength="6" class="form-control" style="text-align: center; text-transform: uppercase; font-weight: 700; letter-spacing: 2px; max-width: 160px; font-size: 1.1rem; padding: 10px 12px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;"><?php echo Helpers::__('chat_lock_btn'); ?></button>
            </form>
        </div>
    <?php else: ?>
        <!-- Chat Desbloqueado -->
        <div class="chat-section" style="border: none; border-radius: 0; height: 480px;">
            <div class="chat-header">
                <div style="display:flex; align-items:center; gap: 8px;">
                    <span style="width: 8px; height: 8px; background-color: var(--color-success); border-radius:50%;"></span>
                    <strong style="font-size: 0.95rem;"><?php echo Helpers::__('chat_title'); ?></strong>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary);">
                    <?php echo Helpers::__('chat_connected_as'); ?>
                </div>
            </div>
            
            <div class="chat-messages" id="chat-box-container">
                <?php if (empty($chatMessages)): ?>
                    <div class="chat-bubble system">
                        <?php echo Helpers::__('chat_empty'); ?>
                    </div>
                <?php else: ?>
                    <?php 
                    foreach ($chatMessages as $msg): 
                        $senderType = $msg['sender_type']; // 'client', 'tech', 'system'
                        $class = $senderType;
                        if ($senderType === 'client') {
                            $class = 'client';
                        } elseif ($senderType === 'tech') {
                            $class = 'tech';
                        } else {
                            $class = 'system';
                        }
                    ?>
                        <div class="chat-bubble <?php echo $class; ?>">
                            <?php if ($senderType !== 'system'): ?>
                                <div style="font-size: 0.72rem; opacity: 0.8; font-weight: 700; margin-bottom: 2px;">
                                    <?php echo htmlspecialchars($msg['sender_name']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div style="font-size: 0.92rem; line-height: 1.4;">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>

                            <?php if (!empty($msg['file_path'])): ?>
                                <div style="margin-top: 8px; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.15); font-size: 0.8rem;">
                                    📎 <a href="<?php echo htmlspecialchars($msg['file_path']); ?>" target="_blank" style="color: inherit; text-decoration: underline; font-weight: 500;">
                                        <?php echo Helpers::__('chat_view_attachment'); ?> (<?php echo basename($msg['file_path']); ?>)
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="chat-meta">
                                <?php echo date('d/m H:i', strtotime($msg['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="chat-input-area">
                <form action="index.php?route=client/chat-send" method="post" enctype="multipart/form-data" class="chat-form">
                    <input type="hidden" name="rma_id" value="<?php echo $rma['id']; ?>">
                    <input type="text" name="message" placeholder="<?php echo Helpers::__('chat_ph'); ?>" required autocomplete="off" class="form-control" style="flex: 1; border-radius: var(--radius-sm);">
                    
                    <label for="chat_attachment" class="btn btn-secondary" style="padding: 10px 14px; cursor: pointer; border-radius: var(--radius-sm);" title="Anexar Foto/Ficheiro">
                        📎
                        <input type="file" name="chat_attachment" id="chat_attachment" style="display:none;" onchange="updateAttachLabel(this)">
                    </label>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px;"><?php echo Helpers::__('chat_btn_send'); ?></button>
                </form>
                <div id="file-selected-info" style="font-size: 0.72rem; color: var(--color-success); margin-top: 5px; display:none;"></div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Fazer scroll automático para o final do chat se este estiver visível
    window.onload = function() {
        var chatBox = document.getElementById('chat-box-container');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    };

    function updateAttachLabel(input) {
        var info = document.getElementById('file-selected-info');
        if (input.files && input.files.length > 0) {
            info.innerHTML = "<?php echo Helpers::__('chat_file_selected'); ?> <strong>" + input.files[0].name + "</strong>";
            info.style.display = "block";
        } else {
            info.style.display = "none";
        }
    }
</script>

