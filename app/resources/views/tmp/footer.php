
    <!-- Essential javascripts for application to work-->
    <script src="<?=JS?>jquery-3.2.1.min.js"></script>
    <script src="<?=JS?>popper.min.js"></script>
    <script src="<?=JS?>bootstrap.min.js"></script>
    <script src="<?=JS?>main.js"></script>
    <script src="<?=JS?>custom.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="<?=JS?>plugins/pace.min.js"></script>
    <script type="text/javascript">
        // Login Page Flipbox control
        $('.login-content [data-toggle="flip"]').click(function() {
        $('.login-box').toggleClass('flipped');
        return false;
        });
    </script>
    </body>
</html>