<?php

if (!defined('INDEX')) {
    die('403-Forbidden Access');
}

$totalSalesStmt = $db->query("
    SELECT COALESCE(SUM(price), 0) AS total
    FROM sp_orders
    WHERE status = 1 AND type = 'file'
");
$totalSales = $totalSalesStmt->fetch(PDO::FETCH_ASSOC)['total'];

$totalOrdersStmt = $db->query("
    SELECT COUNT(*) AS total
    FROM sp_orders
    WHERE status = 1 AND type = 'file'
");
$totalOrders = $totalOrdersStmt->fetch(PDO::FETCH_ASSOC)['total'];

$totalVipStmt = $db->query("
    SELECT COALESCE(SUM(price), 0) AS total
    FROM sp_orders
    WHERE status = 1 AND type = 'vip'
");
$totalVipSales = $totalVipStmt->fetch(PDO::FETCH_ASSOC)['total'];

$dailySalesStmt = $db->query("
    SELECT
        FROM_UNIXTIME(date, '%Y-%m-%d') AS sale_day,
        SUM(price) AS total
    FROM sp_orders
    WHERE status = 1 AND type = 'file'
    GROUP BY sale_day
    ORDER BY sale_day DESC
    LIMIT 30
");
$dailySales = array_reverse($dailySalesStmt->fetchAll(PDO::FETCH_ASSOC));

$topProductsStmt = $db->query("
    SELECT
        f.name AS product_name,
        COUNT(o.id) AS orders_count,
        SUM(o.price) AS total
    FROM sp_orders o
    JOIN sp_files f ON f.id = o.productid
    WHERE o.status = 1 AND o.type = 'file'
    GROUP BY o.productid
    ORDER BY total DESC
    LIMIT 10
");
$topProducts = $topProductsStmt->fetchAll(PDO::FETCH_ASSOC);

$dailyLabels = array_column($dailySales, 'sale_day');
$dailyData = array_map('intval', array_column($dailySales, 'total'));

$productLabels = array_column($topProducts, 'product_name');
$productData = array_map('intval', array_column($topProducts, 'total'));

?>

<div class="overflow-auto h-screen pb-24 px-4 md:px-6">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

        <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-5">
            <h3 class="text-gray-500 dark:text-gray-300 text-sm">فروش محصولات</h3>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-3">
                <?= number_format($totalSales) ?> تومان
            </p>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-5">
            <h3 class="text-gray-500 dark:text-gray-300 text-sm">تعداد سفارش محصول</h3>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-3">
                <?= number_format($totalOrders) ?>
            </p>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-5">
            <h3 class="text-gray-500 dark:text-gray-300 text-sm">فروش اشتراک ویژه</h3>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-3">
                <?= number_format($totalVipSales) ?> تومان
            </p>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">

        <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-5">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5">
                نمودار فروش روزانه
            </h2>
            <canvas id="dailySalesChart"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-5">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5">
                پرفروش‌ترین محصولات
            </h2>
            <canvas id="topProductsChart"></canvas>
        </div>

    </div>

    <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-5 mt-6">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5">
            جدول پرفروش‌ترین محصولات
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-gray-700 dark:text-gray-200">
                <thead>
                    <tr>
                        <th class="p-3">محصول</th>
                        <th class="p-3">تعداد فروش</th>
                        <th class="p-3">مبلغ فروش</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $product) { ?>
                        <tr class="border-t border-gray-200 dark:border-gray-600">
                            <td class="p-3"><?= htmlspecialchars($product['product_name']) ?></td>
                            <td class="p-3"><?= number_format($product['orders_count']) ?></td>
                            <td class="p-3"><?= number_format($product['total']) ?> تومان</td>
                        </tr>
                    <?php } ?>

                    <?php if (empty($topProducts)) { ?>
                        <tr>
                            <td colspan="3" class="p-3 text-center">
                                هنوز فروشی ثبت نشده است.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const dailyLabels = <?= json_encode($dailyLabels, JSON_UNESCAPED_UNICODE) ?>;
const dailyData = <?= json_encode($dailyData) ?>;

new Chart(document.getElementById('dailySalesChart'), {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'فروش روزانه',
            data: dailyData,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

const productLabels = <?= json_encode($productLabels, JSON_UNESCAPED_UNICODE) ?>;
const productData = <?= json_encode($productData) ?>;

new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: productLabels,
        datasets: [{
            label: 'مبلغ فروش',
            data: productData
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
