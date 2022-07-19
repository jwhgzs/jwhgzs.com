<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                
            };
        },
        mounted() {
            setup(['用户中心', '主页']);
            if (getCookie('userToken')) {
                document.location.href = '<?= u('local://user/ucenter') ?>';
            } else {
                document.location.href = '<?= u('local://user/login') ?>';
            }
        },
        methods: {
            
        }
    };
</script>