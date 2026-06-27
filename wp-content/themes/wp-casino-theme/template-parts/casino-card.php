<?php
$bonus = get_post_meta(get_the_ID(), '_casino_bonus', true);
$rating = get_post_meta(get_the_ID(), '_casino_rating', true);
$regions = get_post_meta(get_the_ID(), '_casino_regions', true);
?>

<article <?php post_class('card casino-card'); ?>>
    <h2 class="card__title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h2>

    <?php if ($rating) : ?>
        <p class="casino-card__rating">Rating: <?php echo esc_html($rating); ?>/5</p>
    <?php endif; ?>

    <?php if ($bonus) : ?>
        <p class="casino-card__bonus"><?php echo esc_html($bonus); ?></p>
    <?php endif; ?>

    <?php if ($regions) : ?>
        <p class="casino-card__regions">Available in: <?php echo esc_html($regions); ?></p>
    <?php endif; ?>

    <div class="card__excerpt">
        <?php the_excerpt(); ?>
    </div>

    <a class="card__link" href="<?php the_permalink(); ?>">
        Read review
    </a>
</article>