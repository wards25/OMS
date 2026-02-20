<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<style>
    .scroll-to-top {
        position: fixed;
        left: 28px; /* Move to the left side */
        bottom: 20px;
        right: auto; /* Remove default right positioning */
    }
</style>

<script>
    if ($(window).width() < 1921) { 
        $(".sidebar").addClass("toggled"); 
        $(".nav-link").addClass("collapsed"); 
        $("#collapseUtilities").removeClass("show"); 
    }
</script>

        <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <?php
                        $year = date("Y");
                        $version_query = mysqli_query($conn, "
                            SELECT version 
                            FROM tbl_changelog 
                            ORDER BY
                                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 1), 'v', -1) AS UNSIGNED) DESC,
                                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 2), '.', -1) AS UNSIGNED) DESC,
                                CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC
                            LIMIT 1
                        ");
                        $fetch_version = mysqli_fetch_assoc($version_query);

                        echo '<span>&copy; 2024 - ' . $year . ' RGC OMS ' . $fetch_version['version'] . ' | Developed by CAB</span>';
                        ?>
                    </div>
                </div>
            </footer>
        <!-- End of Footer -->