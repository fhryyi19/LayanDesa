        </div><!-- /.page-content -->
    </main><!-- /.main-content -->
</div><!-- /.admin-wrapper -->

<script src="../assets/js/admin.js" defer></script>
<script>
// Show hamburger on mobile
if (window.innerWidth <= 768) {
    document.getElementById('sidebarToggle').style.display = 'flex';
}
window.addEventListener('resize', function() {
    document.getElementById('sidebarToggle').style.display =
        window.innerWidth <= 768 ? 'flex' : 'none';
});
</script>
</body>
</html>
