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
$pageTitle = Helpers::__('stock_title');
$editMode = isset($editingItem) && !empty($editingItem);
?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;" id="stock-grid">
    <!-- Formulário: Adicionar / Editar Peça -->
    <div>
        <div style="margin-bottom: 20px;">
            <h2 style="margin-bottom: 5px;"><?php echo Helpers::__('stock_title'); ?></h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;"><?php echo Helpers::__('stock_desc'); ?></p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="card" style="background-color: var(--color-error-bg); border-color: var(--color-error); color: var(--color-error); padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.9rem;">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?route=tech/stock<?php echo $editMode ? '&id=' . $editingItem['id'] : ''; ?>" method="post" class="card">
            <h3 style="font-size: 1.1rem; color: var(--accent-color); border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:15px;">
                <?php echo $editMode ? Helpers::__('stock_edit_item') : Helpers::__('stock_add_new'); ?>
            </h3>

            <div class="form-group">
                <label class="form-label" for="comp_name"><?php echo Helpers::__('lbl_stock_name'); ?></label>
                <input type="text" name="name" id="comp_name" class="form-control" required placeholder="Ex: Bateria Compatível iPhone 11" value="<?php echo $editMode ? htmlspecialchars($editingItem['name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="comp_sku"><?php echo Helpers::__('lbl_stock_sku'); ?></label>
                <input type="text" name="sku" id="comp_sku" class="form-control" required placeholder="Ex: BAT-IPH11" value="<?php echo $editMode ? htmlspecialchars($editingItem['sku']) : ''; ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="comp_qty"><?php echo Helpers::__('lbl_stock_qty'); ?></label>
                    <input type="number" name="quantity" id="comp_qty" class="form-control" required min="0" value="<?php echo $editMode ? (int)$editingItem['quantity'] : '1'; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="comp_price"><?php echo Helpers::__('lbl_stock_price'); ?></label>
                    <input type="number" step="0.01" name="price" id="comp_price" class="form-control" required placeholder="0.00" value="<?php echo $editMode ? htmlspecialchars($editingItem['price']) : ''; ?>">
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 10px;">
                <?php if ($editMode): ?>
                    <a href="index.php?route=tech/stock" class="btn btn-secondary" style="padding: 10px 16px; font-size: 0.9rem;"><?php echo Helpers::__('Cancelar'); ?></a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">
                    <?php echo Helpers::__('btn_save_item'); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Tabela Geral de Stock -->
    <div class="card" style="padding: 20px;">
        <h3 style="font-size: 1.15rem; margin-bottom: 20px;"><?php echo Helpers::__('lbl_stock_list'); ?></h3>
        
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th><?php echo Helpers::__('th_sku'); ?></th>
                        <th><?php echo Helpers::__('th_component'); ?></th>
                        <th><?php echo Helpers::__('th_available'); ?></th>
                        <th><?php echo Helpers::__('th_price'); ?></th>
                        <th style="text-align: right;"><?php echo Helpers::__('th_actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stock)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px 0;">
                                <?php echo Helpers::__('O inventário está vazio. Adicione componentes através do formulário ao lado.'); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stock as $item): 
                            $isLowStock = $item['quantity'] <= 3;
                        ?>
                            <tr style="<?php echo $isLowStock ? 'background-color: rgba(239, 68, 68, 0.02);' : ''; ?>">
                                <td style="font-family: monospace; font-weight: 600;">
                                    <?php echo htmlspecialchars($item['sku']); ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                </td>
                                <td>
                                    <?php if ($isLowStock): ?>
                                        <span style="color: var(--color-error); font-weight: 700;">
                                            ⚠️ <?php echo $item['quantity']; ?> <?php echo Helpers::__('unidades'); ?> (<?php echo Helpers::__('lbl_low_stock_badge'); ?>)
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--color-success); font-weight: 600;">
                                            <?php echo $item['quantity']; ?> <?php echo Helpers::__('unidades'); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo number_format($item['price'], 2, ',', ' '); ?> €
                                </td>
                                <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="index.php?route=tech/stock&edit=<?php echo $item['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;" title="<?php echo htmlspecialchars(Helpers::__('btn_edit')); ?>">
                                        <?php echo Helpers::__('btn_edit'); ?>
                                    </a>
                                    
                                    <form action="index.php?route=tech/stock-delete" method="post" onsubmit="return confirm('<?php echo htmlspecialchars(Helpers::__('Deseja eliminar esta peça do stock? A sua eliminação não afetará as fichas técnicas de RMA que já utilizaram este componente anteriormente.')); ?>');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; background-color: var(--color-error-bg); color: var(--color-error); border-color: transparent;" title="<?php echo htmlspecialchars(Helpers::__('btn_delete')); ?>">
                                            🗑️
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

