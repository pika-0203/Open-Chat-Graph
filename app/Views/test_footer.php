<footer>
    <hr>
    <small>Execution Time:
        <?php echo getPerformanceCounter('APP_START_TIME'); ?>
        seconds
    </small>
    <br>
    <small>Peak memory usage:
        <?php echo formatBytesToMegaBytes(memory_get_peak_usage(true)) ?>
        MB
    </small>
</footer>
</body>

</html>