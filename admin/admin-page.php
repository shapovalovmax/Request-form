<div class="wrap">
    <h1>Для использования формы, используйте шорткод: [request-form]</h1>
    <h2>Таблица запросов</h2>
    <span>Здесь отображаются запросы из формы</span>

    <?php
    global $wpdb;
    $result = $wpdb->get_results ( "SELECT * FROM  $this->table_name");

    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

    $limit = 5;
    $offset = ( $pagenum - 1 ) * $limit;
    $total = count($result);
    $num_of_pages = ceil( $total / $limit );
    if ($_GET['page'] == 'request-form' && $_GET['pagenum'] >= 1) {
        $entries = $wpdb->get_results("SELECT * FROM $this->table_name LIMIT $offset, $limit" );
    } else {
        $entries = $wpdb->get_results("SELECT * FROM $this->table_name LIMIT 0, $limit");
    }
    ?>

    <form id="removeTable">
        <button type="submit">УДАЛИТЬ ДАННЫЕ</button>
    </form>

    <table class="admin-table-request">
        <thead>
            <tr>
                <th>Name</th>
                <th>E-mail</th>
                <th>Phone</th>
                <th>Date</th>
            </tr>
        </thead>
        <?php
        if ($entries) { ?>
            <?php foreach ($entries as $page) : ?>
                <tr data-id="<?php echo $page->id; ?>">
                    <td><?php echo $page->name; ?></td>
                    <td><?php echo $page->email; ?></td>
                    <td><?php echo $page->phone; ?></td>
                    <td><?php echo $page->time; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php } ?>
    </table>
    <?php
    $page_links = paginate_links( array(
        'base'      => add_query_arg( 'pagenum', '%#%' ),
        'format'    => '',
        'prev_text' => __( '&laquo;', 'test' ),
        'next_text' => __( '&raquo;', 'test' ),
        'total'     => $num_of_pages,
        'current'   => $pagenum
    ) );
    if ( $page_links ) {
        echo '<div class="tablePagination" style="margin: 1em 0">' . $page_links . '</div>';
    }
    ?>
</div>
<div class="clear"></div>