<?php 
include 'db.php'; 

// Tự động lấy thứ hiện tại trong tuần từ hệ thống (0 = Chủ Nhật, 1 = Thứ 2, ..., 6 = Thứ 7)
$current_w = date('w');
$today_field = ($current_w == 0) ? 'cn' : 'thu' . ($current_w + 1);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, minimum-scale=1.0, maximum-scale=3.0">
    <title>THỜI KHÓA BIỂU HỌC TẬP</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- GIAO DIỆN CHUNG --- */
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; margin: 0; padding: 20px; color: #334155; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        h1 { text-align: center; color: #1e293b; font-size: 26px; margin-top: 10px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .subtitle { text-align: center; color: #64748b; margin-bottom: 25px; font-size: 14px; }
        
        /* --- BỘ CHUYỂN TAB TRÊN MOBILE --- */
        .tab-container { display: flex; justify-content: center; gap: 10px; margin-bottom: 25px; }
        .tab-btn { padding: 10px 20px; border: none; border-radius: 30px; font-weight: bold; cursor: pointer; transition: all 0.3s; background: #e2e8f0; color: #475569; font-size: 14px; }
        .tab-btn.active[data-target="1"] { background: #28a745; color: white; box-shadow: 0 4px 6px rgba(40,167,69,0.2); }
        .tab-btn.active[data-target="2"] { background: #0d6efd; color: white; box-shadow: 0 4px 6px rgba(13,110,253,0.2); }

        /* --- KHUNG THỜI KHÓA BIỂU BÉ --- */
        .kid-section { padding: 20px; border-radius: 12px; margin-bottom: 35px; position: relative; background: #fff; transition: opacity 0.3s; }
        
        /* GIỮ NGUYÊN MÀU TRÂM ANH - ĐỔI MÀU THÀNH PHÁT */
        /* Bé 1: Trâm Anh - Màu Xanh Lá giống code cũ của bạn */
        .kid-1-title { background: #e8f5e9 !important; border: 2px solid #28a745; }
        /* Bé 2: Thành Phát - Đổi sang nền Xanh Dương rõ nét, viền xanh bích */
        .kid-2-title { background: #cfe2ff !important; border: 2px solid #0b5ed7; }

        /* --- NÚT BẤM IN --- */
        .btn-print {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            float: right;
            font-size: 13px;
            font-weight: bold;
            color: #334155;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .btn-print:hover { background: #f8fafc; border-color: #94a3b8; }

        /* --- THIẾT LẬP BẢNG & STICKY HEADER --- */
        .table-responsive { 
            width: 100%; 
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch; 
            margin-top: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            max-height: 70vh; 
            overflow-y: auto;
        }
        table { width: 100%; border-collapse: collapse; min-width: 900px; table-layout: fixed; }
        
        /* Cố định thanh tiêu đề khi cuộn dọc */
        thead th { 
            position: sticky; 
            top: 0; 
            z-index: 10; 
            padding: 12px 10px; 
            font-weight: 700; 
            font-size: 14px;
            color: white !important;
        }
        
        th, td { border: 1px solid #cbd5e1; padding: 12px 10px; text-align: center; font-size: 14px; font-weight: 500; word-wrap: break-word; word-break: break-word; }
        
        /* KÍCH THƯỚC MẶC ĐỊNH TRÊN PC */
        .col-buoi { width: 75px; font-weight: 700; } 
        .col-tiet { width: 115px; font-weight: 500; } 

        /* --- HIGHLIGHT CỘT NGÀY HÔM NAY --- */
        .current-day { 
            background-color: #fffbeb !important; 
        }
        th.current-day { background-color: #f59e0b !important; color: white !important; font-weight: 700; }

        /* --- ĐỊNH DẠNG TEXT MÔN HỌC --- */
        .cell-content {
            display: block;
            line-height: 1.4;
        }
        .subject-empty { color: #94a3b8; font-style: italic; font-size: 12px; font-weight: normal; }

        /* --- ĐỊNH DẠNG KHI XUẤT BẢN IN --- */
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { background: #fff; padding: 0; }
            .container { box-shadow: none; padding: 0; }
            .btn-print, footer, .tab-container, .subtitle { display: none !important; }
            .kid-section { display: block !important; opacity: 1 !important; border: none !important; background: #fff !important; padding: 0; margin: 0; }
            table { width: 100%; border-collapse: collapse; min-width: 100%; table-layout: auto; }
            th, td { border: 1px solid #000 !important; color: #000 !important; padding: 8px; font-weight: 600; }
            th { background-color: #f2f2f2 !important; color: #000 !important; position: static; font-weight: 700; }
            .current-day { background-color: transparent !important; }
        }

        /* --- TỐI ƯU ĐIỆN THOẠI MOBILE (RESPONSIVE) --- */
        @media (max-width: 768px) {
            body { padding: 8px; }
            .container { padding: 15px; border-radius: 8px; }
            h1 { font-size: 20px; }
            .btn-print { float: none; display: block; width: 100%; margin-bottom: 15px; text-align: center; padding: 10px; }
            
            .kid-section { display: none; }
            .kid-section.tab-active { display: block; }

            /* Ép độ rộng tất cả các cột thứ bằng cột Tiết (115px) trên màn hình dọc điện thoại */
            table { min-width: 1070px; /* 75px + (115px * 8 cột còn lại) */ }
            th:not(.col-buoi), td:not([rowspan]) { width: 115px !important; min-width: 115px !important; }
        }
        
        @media (min-width: 769px) {
            .tab-container { display: none; } 
            .kid-section { display: block !important; opacity: 1 !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thời Khóa Biểu Học Tập</h1>
        <div class="subtitle">Học Kỳ 2 • Năm Học 2025-2026</div>
        
        <div class="tab-container">
            <button class="tab-btn active" data-target="1">Trâm Anh 5/4</button>
            <button class="tab-btn" data-target="2">Thành Phát 9/3</button>
        </div>
        
        <?php
        $kids = [
            '1' => ['ten' => 'TRÂM ANH 5/4', 'color' => '#28a745', 'bg_class' => 'kid-1-title'], 
            '2' => ['ten' => 'THÀNH PHÁT 9/3', 'color' => '#0d6efd', 'bg_class' => 'kid-2-title']
        ];

        foreach ($kids as $id_be => $info):
            $ten_be = $info['ten'];
            $main_color = $info['color'];
            $bg_class = $info['bg_class'];
            $tab_active_class = ($id_be == '1') ? 'tab-active' : '';
            
            $th_bg_style = "style='background-color: {$main_color};'";
        ?>
            <div id="section-<?php echo $id_be; ?>" class="kid-section <?php echo $bg_class; ?> <?php echo $tab_active_class; ?>">
                
                <button class="btn-print" onclick="printSchedule('section-<?php echo $id_be; ?>')">🖨️ In lịch này</button>

                <h2 style="color: <?php echo $main_color; ?>; text-align: center; margin-top: 5px; font-size: 20px;">
                    -- <?php echo $ten_be; ?> --
                </h2>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="col-buoi" <?php echo $th_bg_style; ?>>BUỔI</th>
                                <th class="col-tiet" <?php echo $th_bg_style; ?>>TIẾT</th>
                                <th class="<?php echo ($today_field == 'thu2') ? 'current-day' : ''; ?>" <?php echo ($today_field == 'thu2') ? '' : $th_bg_style; ?>>THỨ 2</th>
                                <th class="<?php echo ($today_field == 'thu3') ? 'current-day' : ''; ?>" <?php echo ($today_field == 'thu3') ? '' : $th_bg_style; ?>>THỨ 3</th>
                                <th class="<?php echo ($today_field == 'thu4') ? 'current-day' : ''; ?>" <?php echo ($today_field == 'thu4') ? '' : $th_bg_style; ?>>THỨ 4</th>
                                <th class="<?php echo ($today_field == 'thu5') ? 'current-day' : ''; ?>" <?php echo ($today_field == 'thu5') ? '' : $th_bg_style; ?>>THỨ 5</th>
                                <th class="<?php echo ($today_field == 'thu6') ? 'current-day' : ''; ?>" <?php echo ($today_field == 'thu6') ? '' : $th_bg_style; ?>>THỨ 6</th>
                                <th class="<?php echo ($today_field == 'thu7') ? 'current-day' : ''; ?>" <?php echo ($today_field == 'thu7') ? '' : $th_bg_style; ?>>THỨ 7</th>
                                <th class="<?php echo ($today_field == 'cn') ? 'current-day' : ''; ?>" <?php echo ($today_field == 'cn') ? '' : $th_bg_style; ?>>CHỦ NHẬT</th>
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
                                    <td rowspan="<?php echo $buoi_counts[$current_buoi]; ?>" style="background:#f8f9fa; vertical-align: middle; color: #475569;">
                                        <?php echo nl2br(htmlspecialchars($current_buoi)); ?>
                                    </td>
                                    <?php $displayed_buoi[] = $current_buoi; ?>
                                <?php endif; ?>

                                <td class="col-tiet"><?php echo nl2br(htmlspecialchars($row['tiet'])); ?></td>
                                
                                <td class="<?php echo ($today_field == 'thu2') ? 'current-day' : ''; ?>"><span class="cell-content"><?php echo nl2br(htmlspecialchars($row['thu2'])); ?></span></td>
                                <td class="<?php echo ($today_field == 'thu3') ? 'current-day' : ''; ?>"><span class="cell-content"><?php echo nl2br(htmlspecialchars($row['thu3'])); ?></span></td>
                                <td class="<?php echo ($today_field == 'thu4') ? 'current-day' : ''; ?>"><span class="cell-content"><?php echo nl2br(htmlspecialchars($row['thu4'])); ?></span></td>
                                <td class="<?php echo ($today_field == 'thu5') ? 'current-day' : ''; ?>"><span class="cell-content"><?php echo nl2br(htmlspecialchars($row['thu5'])); ?></span></td>
                                <td class="<?php echo ($today_field == 'thu6') ? 'current-day' : ''; ?>"><span class="cell-content"><?php echo nl2br(htmlspecialchars($row['thu6'])); ?></span></td>
                                <td class="<?php echo ($today_field == 'thu7') ? 'current-day' : ''; ?>"><span class="cell-content"><?php echo nl2br(htmlspecialchars($row['thu7'])); ?></span></td>
                                <td class="<?php echo ($today_field == 'cn') ? 'current-day' : ''; ?>"><span class="cell-content"><?php echo nl2br(htmlspecialchars($row['cn'])); ?></span></td>
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

        <footer style="text-align: center; margin-top: 40px; padding: 15px; color: #94a3b8; font-size: 13px;">
            <p><a href="login.php" style="color: #cbd5e1; text-decoration: none; padding: 5px 10px; background: #475569; border-radius: 4px;">Quản trị hệ thống</a></p>
        </footer>
    </div>

    <script>
    // XỬ LÝ CHUYỂN TAB TRÊN MOBILE
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.kid-section').forEach(s => s.classList.remove('tab-active'));
            
            this.classList.add('active');
            const targetId = this.getAttribute('data-target');
            document.getElementById('section-' + targetId).classList.add('tab-active');
        });
    });

    // TỰ ĐỘNG XỬ LÝ Ô TRỐNG
    document.querySelectorAll('.cell-content').forEach(cell => {
        let text = cell.innerText.trim();
        if (text === "" || text === "-") {
            cell.innerHTML = '<span class="subject-empty">-</span>';
        }
    });

    // HÀM IN ẤN QUA IFRAME 
    function printSchedule(divId) {
        var element = document.getElementById(divId);
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
        doc.write('<html><head><title>In Thời Khóa Biểu</title>' + styles + '</head><body onload="window.print(); window.parent.document.body.removeChild(this.ownerElement);">' + printContents + '</body></html>');
        doc.close();
    }
    </script>
</body>
</html>
