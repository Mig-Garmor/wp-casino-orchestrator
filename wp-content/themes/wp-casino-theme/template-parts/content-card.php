<article <?php post_class('card'); ?>>
    <h2 class="card__title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h2>

    <div class="card__excerpt">
        <?php the_excerpt(); ?>
    </div>

    <a class="card__link" href="<?php the_permalink(); ?>">
        Read more
    </a>
</article>