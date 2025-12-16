<?php
/**
 * Pagination Helper
 * 
 * Provides functions for paginating database queries and rendering pagination UI.
 */

/**
 * Calculate pagination parameters
 * 
 * @param int $total_records Total number of records
 * @param int $per_page Records per page (default 10)
 * @param int $current_page Current page number
 * @return array ['offset' => int, 'per_page' => int, 'current_page' => int, 'total_pages' => int, 'total_records' => int]
 */
function paginate($total_records, $per_page = 10, $current_page = 1) {
    $total_records = max(0, (int) $total_records);
    $per_page = max(1, (int) $per_page);
    $total_pages = max(1, ceil($total_records / $per_page));
    $current_page = max(1, min((int) $current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'offset' => $offset,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'total_records' => $total_records
    ];
}

/**
 * Render Bootstrap 5 pagination controls
 * 
 * @param array $pagination The pagination array from paginate()
 * @param string $base_url The base URL for pagination links (without page param)
 * @param int $max_links Maximum number of page links to show (default 5)
 * @return string HTML for pagination
 */
function render_pagination($pagination, $base_url = '', $max_links = 5) {
    $current_page = $pagination['current_page'];
    $total_pages = $pagination['total_pages'];
    $total_records = $pagination['total_records'];
    
    if ($total_pages <= 1) {
        return '<div class="text-muted small">Showing ' . $total_records . ' record(s)</div>';
    }
    
    // Build the separator for URL params
    $separator = strpos($base_url, '?') === false ? '?' : '&';
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0">';
    
    // Previous button
    if ($current_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($base_url . $separator . 'page=' . ($current_page - 1)) . '">&laquo;</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
    }
    
    // Calculate range of pages to show
    $half = floor($max_links / 2);
    $start = max(1, $current_page - $half);
    $end = min($total_pages, $start + $max_links - 1);
    $start = max(1, $end - $max_links + 1);
    
    // First page + ellipsis
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($base_url . $separator . 'page=1') . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Page numbers
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($base_url . $separator . 'page=' . $i) . '">' . $i . '</a></li>';
        }
    }
    
    // Last page + ellipsis
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($base_url . $separator . 'page=' . $total_pages) . '">' . $total_pages . '</a></li>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($base_url . $separator . 'page=' . ($current_page + 1)) . '">&raquo;</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    // Add record count info
    $from = $pagination['offset'] + 1;
    $to = min($pagination['offset'] + $pagination['per_page'], $total_records);
    $html .= '<div class="text-muted small mt-2">Showing ' . $from . '-' . $to . ' of ' . $total_records . ' record(s)</div>';
    
    return $html;
}

/**
 * Build base URL preserving current GET params except 'page'
 * 
 * @return string The base URL with existing query params
 */
function get_pagination_base_url() {
    $params = $_GET;
    unset($params['page']);
    $query = http_build_query($params);
    $base = strtok($_SERVER['REQUEST_URI'], '?');
    return $base . ($query ? '?' . $query : '');
}
