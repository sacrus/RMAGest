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
$pageTitle = Helpers::__('reports_title');
?>

<!-- Cabeçalho invisível para impressão de relatórios -->
<div class="print-report-header" style="display: none;">
    <div>
        <h2><?php echo Helpers::__('print_tech_report'); ?></h2>
        <p>Período: <?php echo htmlspecialchars($filters['start_date'] ? date('d/m/Y', strtotime($filters['start_date'])) : 'Início'); ?> a <?php echo htmlspecialchars($filters['end_date'] ? date('d/m/Y', strtotime($filters['end_date'])) : 'Fim'); ?></p>
        <p>Oficina: <?php echo htmlspecialchars($siteName); ?></p>
    </div>
    <?php if (!empty($logoUrl)): ?>
        <img src="<?php echo htmlspecialchars($logoUrl); ?>" class="print-logo" alt="Logo">
    <?php endif; ?>
</div>

<div class="no-print" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2><?php echo Helpers::__('reports_title'); ?></h2>
        <p style="color: var(--text-secondary); font-size: 0.95rem;"><?php echo Helpers::__('reports_desc'); ?></p>
    </div>
    <button onclick="window.print()" class="btn btn-secondary">
        <?php echo Helpers::__('btn_print_report'); ?>
    </button>
</div>

<!-- Filtros de Relatório -->
<div class="card no-print" style="padding: 20px; margin-bottom: 30px;">
    <h3 style="font-size: 1.1rem; border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px; color: var(--accent-color);">
        <?php echo Helpers::__('btn_filter'); ?>
    </h3>
    <form action="index.php" method="get" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: end;">
        <input type="hidden" name="route" value="tech/reports">

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size:0.8rem;"><?php echo Helpers::__('lbl_start_date'); ?></label>
            <input type="date" name="start_date" class="form-control" style="font-size: 0.85rem; padding: 10px;" value="<?php echo htmlspecialchars($filters['start_date'] ?? ''); ?>">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size:0.8rem;"><?php echo Helpers::__('lbl_end_date'); ?></label>
            <input type="date" name="end_date" class="form-control" style="font-size: 0.85rem; padding: 10px;" value="<?php echo htmlspecialchars($filters['end_date'] ?? ''); ?>">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size:0.8rem;"><?php echo Helpers::__('th_status'); ?></label>
            <select name="status" class="form-control" style="font-size: 0.85rem; padding: 10px;">
                <option value=""><?php echo Helpers::__('opt_all_statuses'); ?></option>
                <?php foreach (Helpers::getStatuses() as $status): ?>
                    <option value="<?php echo htmlspecialchars($status); ?>" <?php echo (isset($filters['status']) && $filters['status'] === $status) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(Helpers::__($status)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size:0.8rem;"><?php echo Helpers::__('lbl_permissions_selector'); ?></label>
            <select name="tech_id" class="form-control" style="font-size: 0.85rem; padding: 10px;">
                <option value=""><?php echo Helpers::__('opt_all_statuses'); ?></option>
                <?php foreach ($technicians as $tech): ?>
                    <option value="<?php echo $tech['id']; ?>" <?php echo (isset($filters['tech_id']) && $filters['tech_id'] == $tech['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tech['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 10px; font-size: 0.85rem; height: 42px;"><?php echo Helpers::__('btn_filter'); ?></button>
            <a href="index.php?route=tech/reports" class="btn btn-secondary" style="padding: 10px; font-size: 0.85rem; height: 42px; display:flex; align-items:center; justify-content:center;" title="<?php echo htmlspecialchars(Helpers::__('btn_clear_filters')); ?>">❌</a>
        </div>
    </form>
</div>

<!-- Cartões de Resumo e Estatística -->
<div class="stats-grid" style="margin-bottom: 30px;" id="reports-stats">
    <!-- Card 1: Total RMAs filtrados -->
    <div class="stat-card" style="border-left: 4px solid var(--color-info);">
        <div class="stat-icon" style="background-color: var(--color-info);">📊</div>
        <div>
            <div class="stat-val"><?php echo $totals['count']; ?></div>
            <div class="stat-lbl"><?php echo Helpers::__('lbl_rmas_processed'); ?></div>
        </div>
    </div>

    <!-- Card 2: Faturamento Total Recebido -->
    <div class="stat-card" style="border-left: 4px solid var(--color-success);">
        <div class="stat-icon" style="background-color: var(--color-success);">💰</div>
        <div>
            <div class="stat-val"><?php echo number_format($totals['paid_amount'], 2, ',', ' '); ?> €</div>
            <div class="stat-lbl"><?php echo Helpers::__('lbl_total_paid'); ?></div>
        </div>
    </div>

    <!-- Card 3: Faturamento Pendente -->
    <div class="stat-card" style="border-left: 4px solid var(--color-warning);">
        <div class="stat-icon" style="background-color: var(--color-warning);">💶</div>
        <div>
            <div class="stat-val"><?php echo number_format($totals['pending_amount'], 2, ',', ' '); ?> €</div>
            <div class="stat-lbl"><?php echo Helpers::__('lbl_total_pending'); ?></div>
        </div>
    </div>
</div>

<!-- Tabela de Resultados -->
<div class="card" style="padding: 20px;">
    <h3 style="font-size: 1.15rem; margin-bottom: 20px;" class="no-print"><?php echo Helpers::__('tbl_reports_list'); ?></h3>
    
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th><?php echo Helpers::__('th_rma_num'); ?></th>
                    <th><?php echo Helpers::__('th_entry_date'); ?></th>
                    <th><?php echo Helpers::__('th_client'); ?></th>
                    <th><?php echo Helpers::__('th_equipment'); ?></th>
                    <th><?php echo Helpers::__('th_status'); ?></th>
                    <th><?php echo Helpers::__('th_budget'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rmas)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px 0;">
                            <?php echo Helpers::__('msg_no_rmas_found'); ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rmas as $item): ?>
                        <tr>
                            <td>
                                <a href="index.php?route=tech/rma-detail&id=<?php echo $item['id']; ?>" style="font-weight: 700; font-family: monospace;" class="no-print">
                                    <?php echo htmlspecialchars($item['rma_number']); ?>
                                </a>
                                <span class="print-only" style="display:none; font-family: monospace; font-weight:700;">
                                    <?php echo htmlspecialchars($item['rma_number']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($item['client_name'] ?: Helpers::__('msg_gdpr_anonymized')); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars(Helpers::__($item['device_type'])); ?>
                            </td>
                            <td>
                                <?php echo Helpers::getStatusBadge($item['current_status']); ?>
                            </td>
                            <td>
                                <?php if ($item['budget_amount'] > 0): ?>
                                    <strong><?php echo number_format($item['budget_amount'], 2, ',', ' '); ?> €</strong>
                                    <span style="font-size: 0.75rem; display:block; color: <?php echo $item['budget_paid'] == 1 ? 'var(--color-success)' : 'var(--color-warning)'; ?>;">
                                        <?php echo $item['budget_paid'] == 1 ? '✓ ' . Helpers::__('lbl_paid') : '⏳ ' . Helpers::__('lbl_pending'); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">0,00 €</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    @media print {
        .print-only {
            display: inline !important;
        }
        #reports-stats {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 10px !important;
            margin-bottom: 20px !important;
        }
        .stat-card {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
            background: #fff !important;
            color: #000 !important;
            padding: 10px !important;
        }
        .stat-icon {
            display: none !important;
        }
        .stat-val {
            color: #000 !important;
            font-size: 1.25rem !important;
        }
    }
</style>

