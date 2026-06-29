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
$pageTitle = Helpers::__('tech_dashboard_title');
?>

<div class="tech-dashboard">
    <!-- Cabeçalho do Painel -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;" id="dashboard-top-row">
        <div>
            <h2><?php echo Helpers::__('tech_dashboard_title'); ?></h2>
            <p style="color: var(--text-secondary); font-size: 0.95rem;"><?php echo str_replace('{user}', htmlspecialchars($_SESSION['user_name']), Helpers::__('tech_dashboard_welcome')); ?></p>
        </div>
        <div>
            <a href="index.php?route=tech/rma-create" class="btn btn-primary" id="btn-create-rma-tech">
                <?php echo Helpers::__('btn_new_rma_tech'); ?>
            </a>
        </div>
    </div>

    <!-- Secção de Cartões de Estatísticas -->
    <div class="stats-grid">
        <!-- Card 1: RMAs Ativos -->
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--color-info);">🛠️</div>
            <div>
                <div class="stat-val"><?php echo $stats['active_rmas']; ?></div>
                <div class="stat-lbl"><?php echo Helpers::__('stat_rmas_in_progress'); ?></div>
            </div>
        </div>

        <!-- Card 2: Aguarda Cliente -->
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--color-warning);">⏳</div>
            <div>
                <div class="stat-val"><?php echo $stats['waiting_rmas']; ?></div>
                <div class="stat-lbl"><?php echo Helpers::__('stat_waiting_client'); ?></div>
            </div>
        </div>

        <!-- Card 3: Prontos (Reparados) -->
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--color-success);">✓</div>
            <div>
                <div class="stat-val"><?php echo $stats['repaired_rmas']; ?></div>
                <div class="stat-lbl"><?php echo Helpers::__('stat_repaired'); ?></div>
            </div>
        </div>

        <!-- Card 4: Alertas de Stock -->
        <a href="index.php?route=tech/stock" class="stat-card" style="cursor: pointer; transition: all var(--transition-fast);">
            <div class="stat-icon" style="background-color: var(--color-error);">📦</div>
            <div>
                <div class="stat-val"><?php echo $stats['low_stock']; ?></div>
                <div class="stat-lbl"><?php echo Helpers::__('stat_low_stock'); ?></div>
            </div>
        </a>
    </div>

    <!-- Secção da Tabela com Filtros -->
    <div class="card" style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <h3 style="font-size: 1.15rem; margin-bottom: 0;"><?php echo Helpers::__('tbl_general_rma_list'); ?></h3>
            
            <!-- Barra de Filtros (Formulário GET) -->
            <form action="index.php" method="get" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="hidden" name="route" value="tech/dashboard">
                
                <input type="text" name="search" placeholder="<?php echo htmlspecialchars(Helpers::__('ph_search_rmas')); ?>" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" class="form-control" style="max-width: 250px; font-size: 0.85rem; padding: 8px 12px;">
                
                <select name="status" class="form-control" style="max-width: 180px; font-size: 0.85rem; padding: 8px 12px;" onchange="this.form.submit()">
                    <option value=""><?php echo Helpers::__('opt_all_statuses'); ?></option>
                    <?php foreach (Helpers::getStatuses() as $status): ?>
                        <option value="<?php echo htmlspecialchars($status); ?>" <?php echo (isset($filters['status']) && $filters['status'] === $status) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(Helpers::__($status)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.85rem;"><?php echo Helpers::__('btn_filter'); ?></button>
                <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
                    <a href="index.php?route=tech/dashboard" class="btn btn-secondary" style="padding: 8px 12px; font-size: 0.85rem; display: flex; align-items: center;" title="<?php echo htmlspecialchars(Helpers::__('btn_clear_filters')); ?>">❌</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabela Responsiva de RMAs -->
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th><?php echo Helpers::__('th_rma_num'); ?></th>
                        <th><?php echo Helpers::__('th_client'); ?></th>
                        <th><?php echo Helpers::__('th_equipment'); ?></th>
                        <th><?php echo Helpers::__('th_entry_date'); ?></th>
                        <th><?php echo Helpers::__('th_status'); ?></th>
                        <th><?php echo Helpers::__('th_budget'); ?></th>
                        <th style="text-align: right;"><?php echo Helpers::__('th_actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rmas)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px 0;">
                                <?php echo Helpers::__('msg_no_rmas_found'); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rmas as $item): ?>
                            <tr>
                                <td>
                                    <a href="index.php?route=tech/rma-detail&id=<?php echo $item['id']; ?>" style="font-weight: 700; font-family: monospace;">
                                        <?php echo htmlspecialchars($item['rma_number']); ?>
                                    </a>
                                </td>
                                <td>
                                    <div><strong><?php echo htmlspecialchars($item['client_name'] ?: Helpers::__('msg_gdpr_anonymized')); ?></strong></div>
                                    <div style="font-size: 0.78rem; color: var(--text-secondary);"><?php echo htmlspecialchars($item['client_contact'] ?: ''); ?></div>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars(Helpers::__($item['device_type'])); ?></div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted); font-family: monospace;"><?php echo htmlspecialchars($item['serial_number'] ?: 'S/N: N/A'); ?></div>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                                </td>
                                <td>
                                    <?php echo Helpers::getStatusBadge($item['current_status']); ?>
                                </td>
                                <td>
                                    <?php if ($item['budget_amount'] > 0): ?>
                                        <strong><?php echo number_format($item['budget_amount'], 2, ',', ' '); ?> €</strong>
                                        <div style="font-size: 0.75rem; color: <?php echo $item['budget_paid'] == 1 ? 'var(--color-success)' : 'var(--color-warning)'; ?>;">
                                            <?php echo $item['budget_paid'] == 1 ? Helpers::__('lbl_paid') : Helpers::__('lbl_pending'); ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.85rem;"><?php echo Helpers::__('lbl_not_entered'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="index.php?route=tech/rma-detail&id=<?php echo $item['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; display: inline-flex; align-items: center;" title="<?php echo htmlspecialchars(Helpers::__('btn_open')); ?>">
                                        <?php echo Helpers::__('btn_open'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

