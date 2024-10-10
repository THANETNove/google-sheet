<script>
    // แสดงปุ่มเมื่อเลื่อนลง
    window.onscroll = function() {
        const button = document.getElementById('scrollToTop');
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            button.style.display = "block";
        } else {
            button.style.display = "none";
        }
    };

    // เมื่อคลิกปุ่มเลื่อนกลับไปยังด้านบน
    document.getElementById('scrollToTop').onclick = function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };
</script>
