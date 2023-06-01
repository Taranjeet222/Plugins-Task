<?php 
session_start();
require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
class Calendar extends WP_List_Table{
    private $currentMonth;
    private $currentYear;
    public function __construct(){
        parent::__construct(array(
            'singular' => 'calendar_item',
            'plural'   => 'calendar_items',
            // Additional arguments
        ));
        $this->currentMonth = date('n');
        $this->currentYear = date('Y');
        add_action( 'admin_menu', array($this,'add_calendar_menu' )); 
    } 
    
    public function prepare_items(){
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $days = array();
        $currentDay = 1;
        // $currentMonth = date('n');
        // $this->currentYear = date('Y');
        $this->currentMonth = isset($_SESSION['calendar_month']) ? $_SESSION['calendar_month'] : date('n');
        $this->currentYear = isset($_SESSION['calendar_year']) ? $_SESSION['calendar_year'] : date('Y');
        if(isset($_POST['calendar_month']) && isset($_POST['calendar_year']))
        {
            $this->currentMonth = $_POST['calendar_month'];
            $this->currentYear = $_POST['calendar_year'];
        }
        $firstDay = date('w', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));
        $no_days = cal_days_in_month(CAL_GREGORIAN,$this->currentMonth,$this->currentYear);
        for ($week = 0; $week < 6; $week++) {
            if($currentDay > $no_days)
            break;
            $days[$week] = array();
            if($week===0)
            {
                for($i=0;$i<$firstDay;$i++){
                    $days[$week][$i] = '';
                }
                for ($weekday = $firstDay; $weekday < 7; $weekday++) {
                    if ($currentDay > $no_days) {
                        $days[$week][$weekday] = '';
                    } else {
                        $days[$week][$weekday] = $currentDay;
                        $currentDay++;
                    }
                }
            }
            else{
                for ($weekday = 0; $weekday <7; $weekday++) {
                    if ($currentDay > $no_days) {
                        $days[$week][$weekday] = '';
                    } else {
                        $days[$week][$weekday] = $currentDay;
                        $currentDay++;
                    }
                }
            }
        }
        $this->items = $days;
    }
    public function fetch_data($date) {

        global $wpdb;

        $table_name = $wpdb->prefix . 'content_calendar';
        $data = $wpdb->get_results("SELECT * FROM $table_name WHERE date='$date' ");
    
        return $data;
    }
    
    public function get_columns() {
        
        $columns = array(
            '0' => 'Sunday',
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday'
        );
        
        return $columns;
    }
    public function display_form($column_name, $date) {
        $d = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . $date));
       
        $form_id = 'form_' . $column_name . '_' . $date;
        $edit_form_id = 'edit_submit_'.$d;
        $output = '<div class="calendar-item" onclick="ShowEditForm(\'' . $edit_form_id . '\')">';
        $output .= '<p class="cal-date">'. $date .'</p>';
        $output .= '<div class="date-area">';
        $output .= '<div id="' . $form_id . '" class="form-area hide">';
        $output .= '<form method="post">'; 
        $output .= '<p ><span class="fas fa-file-alt"></span> Post title:</p>';
        $output .= '<input type="text" name="post_title">';

        $output .= '<p ><span class="fas fa-user"></span> Author:</p>';
        $output .= '<input type="text" name="author">';

        $output .= '<p ><span class="fas fa-eye"></span> Reviewer:</p>';
        $output .= '<input type="text" name="reviewer">';

        $output .= '<p ><span class="fas fa-calendar"></span> Occasion:</p>';
        $output .= '<input type="text" name="occasion">';
        $name = 'info_submit_'.$d;
        $output .= '<input class="form-submit" type="submit" name="' . $name . '" value="Submit">';

        $output .= '</form>';
        $output .= '</div>';
        $output .= '</div>';
        if (isset($_POST[$name])) {
            $post_title = $_POST['post_title'];
            $author = $_POST['author'];
            $reviewer = $_POST['reviewer'];
            $occasion = $_POST['occasion'];
    
            $this->store_data($post_title, $author, $reviewer, $occasion, $d);
        }
        $data = $this->fetch_data($d);
        $output .= '<div class="fetched-data">';
        $output .= '<ul class="custom-list">';
        foreach ($data as $row) {
            $output .= '<li>' . $row->post_title .'</li>';
        }

        $output .= '</ul>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<p class="add-event" onclick="showForm(\'' . $form_id . '\')">Add event</p>';
        echo $output;
    }
    public function store_data($post_title, $author, $reviewer, $occasion, $date) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'content_calendar';
    
        $data = array(
            'post_title' => $post_title,
            'author' => $author,
            'reviewer' => $reviewer,
            'occasion' => $occasion,
            'date' => $date
        );
    
        $wpdb->insert($table_name, $data);
    }
    public function edit_form($date){
        $data = $this->fetch_data($date);
        $output = '';
        $id = 'edit_submit_'.$date;
        $output .= '<div class="edit-form hide" id="'.$id.'">';
        foreach ($data as $row) {
            $name = 'edit_submit_'.$row->post_title;
            $output .= '<form method="post" class="display-edit-form" >
                        <p><span class="fas fa-file-alt"></span> Post title:</p>
                        <input type="text" name="post_title" value="'.$row->post_title.'">
                        
                        <p><span class="fas fa-user"></span> Author:</p>
                        <input type="text" name="author" value="'.$row->author.'">

                        <p><span class="fas fa-eye"></span> Reviewer:</p>
                        <input type="text" name="reviewer" value="'.$row->reviewer.'">

                        <p><span class="fas fa-calendar"></span> Occasion:</p>
                        <input type="text" name="occasion" value="'.$row->occasion.'">
                    ';
                    $del_name = 'delete_'. $name;
            $output .= '<div class="panel">';
            $output .= '<input  type="submit" name="' . $name . '" value="Submit">';
            $output .= '<button class="del-button" type="submit" name="' . $del_name . '" >Delete</button>';
            $output .= '</div>';
            $output .= '</form>';
            if(isset($_POST[$del_name]))
            {
                $this->delete_form($row->post_title);
                echo '<script>window.location.reload();</script>';
            }
            if (isset($_POST[$name])) {
                $old_name = $row->post_title;
                $post_title = $_POST['post_title'];
                $author = $_POST['author'];
                $reviewer = $_POST['reviewer'];
                $occasion = $_POST['occasion'];
                $this->update_form($old_name,$post_title, $author, $reviewer, $occasion, $date);
                echo '<script>window.location.reload();</script>';
            }
        }
        $output .= '</div>';
        echo $output;
    }
    public function delete_form($post_title) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'content_calendar';
        
        $wpdb->delete($table_name, array('post_title' => $post_title));
    }
    public function update_form($old_name,$post_title, $author, $reviewer, $occasion, $date){
        global $wpdb;
        $table_name = $wpdb->prefix . 'content_calendar';

        $data = array(
            'post_title' => $post_title,
            'author' => $author,
            'reviewer' => $reviewer,
            'occasion' => $occasion,
        );
        error_log($old_name);
        $where = array('post_title' => $old_name);
    
        $wpdb->update($table_name, $data, $where);
    }
    
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
                if($item[$column_name]==='')
                return '';
                ob_start();
                $this->display_form($column_name,$item[$column_name]);
                $form_output = ob_get_clean();
                return $form_output;
            default:
                return '';
        }
    }
    
}

class Add_Menu{
    public function __construct(){
        add_action( 'admin_menu', array($this,'add_calendar_menu' )); 
    }
    public function add_calendar_menu() {
        $page_title = 'Content Calendar';
        $menu_title = 'Content Calendar';
        $capability = 'edit_pages';
        $menu_slug  = 'content-calendar';
        $function   = array($this, 'display_calendar_page');
        $icon_url   = 'dashicons-calendar';
        $position   = 101;
        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
    }
    public function display_calendar_page() {
        
        $Calendar_obj = new Calendar();
        $Calendar_obj->prepare_items();
        $output = '';
        ob_start();
        $currentMonth = date('n');
        $currentYear = date('Y');

        $currentMonth = isset($_SESSION['calendar_month']) ? $_SESSION['calendar_month'] : date('n');
        $currentYear = isset($_SESSION['calendar_year']) ? $_SESSION['calendar_year'] : date('Y');
        if(isset($_POST['calendar_month']) && isset($_POST['calendar_year']))
        {
            $currentMonth = $_POST['calendar_month'];
            $currentYear = $_POST['calendar_year'];
            $_SESSION['calendar_month'] = $currentMonth;
            $_SESSION['calendar_year'] = $currentYear;
        }
        $no_days = cal_days_in_month(CAL_GREGORIAN,$currentMonth,$currentYear);
        ?>
        <div class="wrap">
        <h1>Content Calendar</h1>
        <form method="post" class="select-month-year">
        <label for="calendar_month" id="month-label">Month:</label>
        <select name="calendar_month" id="calendar_month">
        <option value="1" <?php if ($currentMonth == 1) echo 'selected'; ?>>January</option>
        <option value="2" <?php if ($currentMonth == 2) echo 'selected'; ?>>February</option>
        <option value="3" <?php if ($currentMonth == 3) echo 'selected'; ?>>March</option>
        <option value="4" <?php if ($currentMonth == 4) echo 'selected'; ?>>April</option>
        <option value="5" <?php if ($currentMonth == 5) echo 'selected'; ?>>May</option>
        <option value="6" <?php if ($currentMonth == 6) echo 'selected'; ?>>June</option>
        <option value="7" <?php if ($currentMonth == 7) echo 'selected'; ?>>July</option>
        <option value="8" <?php if ($currentMonth == 8) echo 'selected'; ?>>August</option>
        <option value="9" <?php if ($currentMonth == 9) echo 'selected'; ?>>September</option>
        <option value="10" <?php if ($currentMonth == 10) echo 'selected'; ?>>October</option>
        <option value="11" <?php if ($currentMonth == 11) echo 'selected'; ?>>November</option>
        <option value="12" <?php if ($currentMonth == 12) echo 'selected'; ?>>December</option>
        </select>

        <label for="calendar_year" id="year-label">Year:</label>
        <select name="calendar_year" id="calendar_year">
        <?php for ($year = 1950; $year <= $currentYear; $year++) {
           echo '<option value="' . $year . '"';
           if ($year == $currentYear) echo 'selected';
           echo '>' . $year . '</option>';
        }?>
        </select>
        <input type="submit" id="select-month-year-submit" value="Submit">
        </form>

        <?php
        $mon = array(
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        );
        echo '<h2>'.$mon[$currentMonth].' '.$currentYear.'</h2>';
        $Calendar_obj->display();
        for($i=1;$i<=$no_days;$i++)
        {
            $d = date('Y-m-d', strtotime($currentYear . '-' . $currentMonth . '-' . $i));
            $Calendar_obj->edit_form($d);
        }
        $output .= ob_get_clean();
        echo $output;
    }
}