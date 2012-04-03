<div class="fortune" id="f<?php echo $cookie->getId(); ?>">
    <h2>
        <a href="?view=one&amp;id=<?php echo $cookie->getId(); ?>">#f<?php echo $cookie->getId(); ?></a>
        (<?php echo $cookie->getDate(); ?>)
    </h2>
    
    <div class="vote" id="vote<?php echo $cookie->getId(); ?>">vote <span><?php echo $cookie->getVote(); ?></span>
    <?php if (!isset($votes[$cookie->getId()])): ?>
        <form action="api.php" method="post" class="vote-form">
        <input type="hidden" name="id" value="<?php echo $cookie->getId(); ?>" />
        <input class="submit bury" type="submit" name="bury" value="-" title="-1" />
        <input class="submit vote" type="submit" name="vote" value="+" title="+1" />
        </form>
    <?php endif; ?>
    </div>
    <?php echo $cookie->getHTML(); ?>
</div>