<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, minimum-scale=1.0, maximum-scale=3.0">
    <title>THỜI KHÓA BIỂU HỌC TẬP</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS Giao diện chung */
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 15px; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { text-align: center; color: #334155; font-size: 24px; margin-bottom: 25px; }
        
        .kid-section { padding: 15px; border-radius: 8px; margin-bottom: 30px; position: relative; background: #fff; }
        
        /* CSS Bảng và tính năng Cuộn Ngang trên Mobile */
        .table-responsive { 
            width: 100%; 
            overflow-x: auto; /* Kích hoạt cuộn ngang khi bảng quá rộng */
            -webkit-overflow-scrolling: touch; /* Cuộn mượt mà trên iOS */
            margin-top: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        table { width: 100%; border-collapse: collapse; min-width: 750px; /* Ép bảng giữ độ rộng tối thiểu trên mobile để không bị bóp nghẹt chữ */ }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 14px; white-space: nowrap; /* Tránh tự động xuống dòng lung tung */ }
        
        /* Cố định chiều rộng 2 cột đầu */
        .col-buoi { width: 70px; }
        .col-tiet { width: 55px; }

        /* Nút in */
        .btn-print {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            float: right;
            font-size: 13px;
            font-weight: bold;
            color: #334155;
            transition: all 0.2s;
        }
        .btn-print:hover { background: #f8fafc; }

        /* TÙY CHỈNH MÀU SẮC ĐẶC TRƯNG CHO TỪNG BÉ (TIÊU ĐỀ KHÁC NHAU) */
        /* Bé 1: Trâm Anh - Màu Xanh Lá (Tươi tắn, nhẹ nhàng) */
        .kid-1-title { background: #e8f5e9 !important; border: 2px solid #28a745; }
        /* Bé 2: Thành Phát - Màu Xanh Dương (Mạnh mẽ, năng động) */
        .kid-2-title { background: #e3f2fd !important; border: 2px solid #007bff; }

        /* CSS khi IN (Giữ nguyên cấu trúc ẩn các thành phần thừa) */
        @media print {
            @page { size: A4 landscape; margin: 15mm; }
            body { background: #fff; padding: 0; }
            .btn-print, footer { display: none !important; }
            table { width: 100%; border-collapse: collapse; min-width: 100%; }
            th, td { border: 1px solid #000 !important; color: #000 !important; padding: 8px; white-space: normal; }
            th { background-color: #f2f2f2 !important; color: #000 !important; }
        }

        /* Tối ưu hóa thêm một chút CSS khi xem trên màn hình nhỏ điện thoại */
        @media (max-width: 600px) {
            body { padding: 5px; }
            .container { padding: 10px; }
            h1 { font-size: 18px; }
            .kid-section h2 { font-size: 16px; margin-bottom: 10px; }
            .btn-print { float: none; display: block; width: 100%; margin-bottom: 15px; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>THỜI KHÓA BIỂU HỌC TẬP HK2 2025-2026</h1>
        
        <?php
        // Cấu hình thông tin riêng biệt, bao gồm cả class màu nền tiêu đề đặc trưng
        $kids = [
            '1' => ['ten' => 'TRÂM ANH 5/4', 'color' => '#28a745', 'bg_class' => 'kid-1-title'], 
            '2' => ['ten' => 'THÀNH PHÁT 9/3', 'color' => '#007bff', 'bg_class' => 'kid-2-title']
        ];

        foreach ($kids as $id_be => $info):
            $ten_be = $info['ten'];
            $main_color = $info['color'];
            $bg_class = $info['bg_class'];
        ?>
            <div id="section-<?php echo $id_be; ?>" class="kid-section <?php echo $bg_class; ?>">
                
                <button class="btn-print" onclick="printSchedule('section-<?php echo $id_be; ?>')">🖨️ In bản này</button>

                <h2 style="color: <?php echo $main_color; ?>; text-align: center; margin-top: 5px;">
                    -- <?php echo $ten_be; ?> --
                </h2>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr style="background-color: <?php echo $main_color; ?>; color: white;">
                                <th class="col-buoi">BUỔI</th>
                                <th class="col-tiet">TIẾT</th>
                                <th>THỨ 2</th>
                                <th>THỨ 3</th>
                                <th>THỨ 4</th>
                                <th>THỨ 5</th>
                                <th>THỨ 6</th>
                                <th>THỨ 7</th>
                                <th>CN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM schedule WHERE be_name = ? ORDER BY FIELD(buoi, 'SÁNG', 'CHIỀU', 'TỐI'), id ASC");
                            $stmt->execute([$id_be]);
                            $rows = $stmt->fetchAll();
                            
                            if (count($rows) > 0):
                                $buoi_counts = array_count_values(array_column($rows, 'buoi'));
                                $displayed_buoi = [];

                                foreach ($rows as $row):
                                    $current_buoi = $row['buoi'];
                            ?>
                            <tr>
                                <?php if (!in_array($current_buoi, $displayed_buoi)): ?>
                                    <td rowspan="<?php echo $buoi_counts[$current_buoi]; ?>" style="font-weight:bold; background:#f8f9fa; vertical-align: middle;">
                                        <?php echo htmlspecialchars($current_buoi); ?>
                                    </td>
                                    <?php $displayed_buoi[] = $current_buoi; ?>
                                <?php endif; ?>

                                <td style="background:#fff3cd; font-weight:bold;"><?php echo htmlspecialchars($row['tiet']); ?></td>
                                <td><?php echo htmlspecialchars($row['thu2']); ?></td>
                                <td><?php echo htmlspecialchars($row['thu3']); ?></td>
                                <td><?php echo htmlspecialchars($row['thu4']); ?></td>
                                <td><?php echo htmlspecialchars($row['thu5']); ?></td>
                                <td><?php echo htmlspecialchars($row['thu6']); ?></td>
                                <td><?php echo htmlspecialchars($row['thu7']); ?></td>
                                <td><?php echo htmlspecialchars($row['cn']); ?></td>
                            </tr>
                            <?php 
                                endforeach; 
                            else: 
                            ?>
                                <tr><td colspan="9" style="padding:20px; color:#999; text-align:center;">Chưa có dữ liệu thời khóa biểu cho <?php echo $ten_be; ?>.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>

        <footer style="text-align: center; margin-top: 50px; padding: 20px; color: #888;">
            <p><a href="login.php" style="color: #aaa; text-decoration: none;">Quản trị hệ thống</a></p>
        </footer>
    </div>

    <script>
    function printSchedule(divId) {
        var element = document.getElementById(divId);
        // Sao chép nội dung
        var printContents = element.innerHTML;
        
        var styles = '';
        var styleTags = document.querySelectorAll('style, link[rel="stylesheet"]');
        styleTags.forEach(function(tag) {
            styles += tag.outerHTML;
        });

        var iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);

        var doc = iframe.contentWindow.document;
        doc.open();
        // Loại bỏ class nền đặc trưng khi in để tránh làm tốn mực in và bảng sạch hơn
        doc.write('<html><head><title>In Thời Khóa Biểu</title>' + styles + '</head><body onload="window.print(); window.parent.document.body.removeChild(this.ownerElement);">' + printContents + '</body></html>');
        doc.close();
    }
    </script>
</body>
</html>
