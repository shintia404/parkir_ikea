document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.parkir-menu-link');

    links.forEach(function (link) {
        try {
            const linkUrl = new URL(link.href, window.location.href);
            const currentPath = window.location.pathname;
            const hrefPath = linkUrl.pathname;

            const controllerMatch = currentPath.includes('/controller/c_kendaraan.php') && hrefPath.includes('/controller/c_kendaraan.php');
            const tarifControllerMatch = currentPath.includes('/controller/c_tarif.php') && hrefPath.includes('/controller/c_tarif.php');
            const transaksiControllerMatch = currentPath.includes('/controller/c_transaksi.php') && hrefPath.includes('/controller/c_transaksi.php');
            const exactViewMatch = currentPath.endsWith(hrefPath.split('/').pop());

            if (controllerMatch || tarifControllerMatch || transaksiControllerMatch || exactViewMatch) {
                link.classList.add('active');
            }
        } catch (error) {
            // Link tetap berfungsi meskipun URL tidak dapat diproses.
        }
    });
});
