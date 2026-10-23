    </div> <!-- /.main-content -->

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var dropdowns = document.getElementsByClassName("dropdown-btn");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].addEventListener("click", function () {
                    this.classList.toggle("active");
                    var content = this.nextElementSibling;
                    content.style.display = (content.style.display === "block") ? "none" : "block";
                });
            }
        });
    </script>

</body>
</html>