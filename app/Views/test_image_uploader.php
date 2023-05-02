<main>
    <hr>
    <section>
        <!-- Image upload form !-->
        <form action="/image/store" method="POST" enctype="multipart/form-data">
            <label for="file">Choose a file:</label>
            <input type="file" name="file" id="file">
            <label>Image Type:</label>
            <input type="radio" name="imageType" id="image-WEBP" value="WEBP" checked>
            <label for="image-WEBP">WEBP</label>
            <input type="radio" name="imageType" id="image-JPG" value="JPG">
            <label for="image-JPG">JPG</label>
            <input type="radio" name="imageType" id="image-PNG" value="PNG">
            <label for="image-PNG">PNG</label>
            <br>
            <label>Image Size:</label>
            <input type="radio" name="imageSize" id="image-size-640" value="640" checked>
            <label for="image-size-640">Up to 640px</label>
            <input type="radio" name="imageSize" id="image-size-1000" value="1000">
            <label for="image-size-1000">Up to 1000px</label>
            <input type="radio" name="imageSize" id="image-size-max" value="0">
            <label for="image-size-max">Max size</label>
            <br>
            <input type="submit" value="Upload">
        </form>
    </section>
    <!-- Show error !-->
    <?php if (session()->hasError()) : ?>
        <header>
            <?php foreach (session()->getError() as $key => $error) : ?>
                <p><?php echo $error['code'] . ': ' . $error['message'] ?></p>
            <?php endforeach ?>
        </header>
    <?php endif ?>
    <!-- Show file infomation !-->
    <?php if (session()->has('image')) : ?>
        <hr>
        <section>
            <figure>
                <img style="display:block" src="<?php echo url('images/') . session('image') ?>">
                <figcaption>
                    <i>
                        <small><?php echo '/images/' . session('image') ?></small>
                    </i>
                    <br>
                    <!-- Verify existence of file to avoid errors !-->
                    <?php if (file_exists($path = publicDir('images/') . session('image'))) : ?>
                        <i>
                            <small>
                                <?php list($width, $height, $type, $attr) = getimagesize($path) ?>
                                <?php echo "Format: " . image_type_to_extension($type, false) . " | Resolution: $width x $height | Size: " . round(filesize($path) / 1024, 2) . " KB" ?>
                            </small>
                        </i>
                    <?php endif ?>
                </figcaption>
            </figure>
        </section>
    <?php endif ?>
</main>