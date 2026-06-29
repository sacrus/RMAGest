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
$pageTitle = Helpers::__('home_title');
?>

<section class="client-hero">
    <div class="hero-logo-box">
        <h1 class="hero-title"><?php echo htmlspecialchars($siteName); ?></h1>
        <p class="hero-subtitle"><?php echo Helpers::__('home_subtitle'); ?></p>
    </div>
    
    <?php if (isset($_GET['error']) && $_GET['error'] === 'not_found'): ?>
        <div class="card" style="background-color: var(--color-error-bg); border-color: var(--color-error); color: var(--color-error); padding: 12px 24px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 500; font-size: 0.95rem; width: 100%; max-width: 580px; text-align: left;">
            ⚠️ <?php echo Helpers::__('home_error_not_found'); ?>
        </div>
    <?php endif; ?>

    <div class="search-box-container">
        <form action="index.php" method="get" class="search-form-google">
            <input type="hidden" name="route" value="client/rma-view">
            <div class="search-bar">
                <span class="search-icon-inside">🔍</span>
                <input type="text" name="rma" class="search-input" placeholder="<?php echo Helpers::__('home_search_placeholder'); ?>" required autocomplete="off" id="search-input-field">
            </div>
            
            <div class="search-actions">
                <button type="submit" class="btn btn-primary" id="btn-search-submit"><?php echo Helpers::__('home_search_btn'); ?></button>
                <a href="index.php?route=client/new-rma" class="btn btn-secondary" id="btn-new-rma-action"><?php echo Helpers::__('home_request_btn'); ?></a>
            </div>
        </form>
    </div>
    
    <div style="margin-top: 50px; display: flex; gap: 40px; color: var(--text-muted); font-size: 0.9rem;">
        <div><?php echo Helpers::__('home_feat_realtime'); ?></div>
        <div><?php echo Helpers::__('home_feat_chat'); ?></div>
        <div><?php echo Helpers::__('home_feat_email'); ?></div>
    </div>
</section>

