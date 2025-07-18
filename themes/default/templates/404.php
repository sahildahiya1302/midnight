<?php
declare(strict_types=1);
ob_start();
?>
<section class="page-404" role="main" aria-labelledby="error-title">
    <h1 id="error-title">404 - Page Not Found</h1>
    <p>Sorry, the page you are looking for does not exist.</p>
    <p><a href="/">Return to homepage</a></p>
</section>
<style>
.page-404 {
    text-align: center;
    padding: 100px 20px;
}
.page-404 h1 {
    font-size: 3em;
    margin-bottom: 20px;
}
.page-404 p {
    font-size: 1.2em;
    margin-bottom: 10px;
}
.page-404 a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}
.page-404 a:hover {
    text-decoration: underline;
}
</style>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/theme.php";
