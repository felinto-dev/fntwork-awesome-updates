<?php
namespace MOD\Controller;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;
use MOD\Helper\MOD_Utils as Utils;
use MOD\View\MOD_Settings as Settings_View;
use MOD\Model\MOD_Setting as Settings_Model;

class MOD_Settings {
	public function __construct() {
        $this->page_name      = 'mod-auto-updates';
        $this->page_log       = 'mod-auto-updates-log';
        $this->settings_model = new Settings_Model();
        $this->is_log_menu    = Utils::mod_is_log_page();

        add_action( 'admin_menu', [ $this, 'mod_add_plugin_page' ] );
        add_action( 'admin_init', [ $this, 'mod_page_init' ] );
    }
    /**
     * Add options page
     */
    public function mod_add_plugin_page() {
        add_menu_page(
            __( 'Configurações', Core::TEXTDOMAIN ),
            __( 'Atualizações Fantásticas', Core::TEXTDOMAIN ),
            'manage_options',
            $this->page_name,
            [ $this, 'mod_create_admin_page' ],
            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgd2lkdGg9IjU0OC4xNzZweCIgaGVpZ2h0PSI1NDguMTc2cHgiIHZpZXdCb3g9IjAgMCA1NDguMTc2IDU0OC4xNzYiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU0OC4xNzYgNTQ4LjE3NjsiDQoJIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPHBhdGggZD0iTTUyNC4zMjYsMjk3LjM1MmMtMTUuODk2LTE5Ljg5LTM2LjIxLTMyLjc4Mi02MC45NTktMzguNjg0YzcuODEtMTEuOCwxMS43MDQtMjQuOTM0LDExLjcwNC0zOS4zOTkNCgkJYzAtMjAuMTc3LTcuMTM5LTM3LjQwMS0yMS40MDktNTEuNjc4Yy0xNC4yNzMtMTQuMjcyLTMxLjQ5OC0yMS40MTEtNTEuNjc1LTIxLjQxMWMtMTguMDgzLDAtMzMuODc5LDUuOTAxLTQ3LjM5LDE3LjcwMw0KCQljLTExLjIyNS0yNy40MS0yOS4xNzEtNDkuMzkzLTUzLjgxNy02NS45NWMtMjQuNjQ2LTE2LjU2Mi01MS44MTgtMjQuODQyLTgxLjUxNC0yNC44NDJjLTQwLjM0OSwwLTc0LjgwMiwxNC4yNzktMTAzLjM1Myw0Mi44Mw0KCQljLTI4LjU1MywyOC41NDQtNDIuODI1LDYyLjk5OS00Mi44MjUsMTAzLjM1MWMwLDIuNDc0LDAuMTkxLDYuNTY3LDAuNTcxLDEyLjI3NWMtMjIuNDU5LDEwLjQ2OS00MC4zNDksMjYuMTcxLTUzLjY3Niw0Ny4xMDYNCgkJQzYuNjYxLDI5OS41OTQsMCwzMjIuNDMsMCwzNDcuMTc5YzAsMzUuMjE0LDEyLjUxNyw2NS4zMjksMzcuNTQ0LDkwLjM1OGMyNS4wMjgsMjUuMDM3LDU1LjE1LDM3LjU0OCw5MC4zNjIsMzcuNTQ4aDMxMC42MzYNCgkJYzMwLjI1OSwwLDU2LjA5Ni0xMC43MTEsNzcuNTEyLTMyLjEyYzIxLjQxMy0yMS40MDksMzIuMTIxLTQ3LjI0NiwzMi4xMjEtNzcuNTE2QzU0OC4xNzIsMzM5Ljk0NCw1NDAuMjIzLDMxNy4yNDgsNTI0LjMyNiwyOTcuMzUyDQoJCXogTTM2Mi41OTUsMzA4LjM0NEwyNjIuMzgsNDA4LjU2NWMtMS43MTEsMS43MDctMy45MDEsMi41NjYtNi41NjcsMi41NjZjLTIuNjY0LDAtNC44NTQtMC44NTktNi41NjctMi41NjZMMTQ4Ljc1LDMwOC4wNjMNCgkJYy0xLjcxMy0xLjcxMS0yLjU2OC0zLjkwMS0yLjU2OC02LjU2N2MwLTIuNDc0LDAuOS00LjYxNiwyLjcwOC02LjQyM2MxLjgxMi0xLjgwOCwzLjk0OS0yLjcxMSw2LjQyMy0yLjcxMWg2My45NTRWMTkxLjg2NQ0KCQljMC0yLjQ3NCwwLjkwNS00LjYxNiwyLjcxMi02LjQyN2MxLjgwOS0xLjgwNSwzLjk0OS0yLjcwOCw2LjQyMy0yLjcwOGg1NC44MjNjMi40NzgsMCw0LjYwOSwwLjksNi40MjcsMi43MDgNCgkJYzEuODA0LDEuODExLDIuNzA3LDMuOTUzLDIuNzA3LDYuNDI3djEwMC40OTdoNjMuOTU0YzIuNjY1LDAsNC44NTUsMC44NTUsNi41NjMsMi41NjZjMS43MTQsMS43MTEsMi41NjIsMy45MDEsMi41NjIsNi41NjcNCgkJQzM2NS40MzgsMzAzLjc4OSwzNjQuNDk0LDMwNi4wNjQsMzYyLjU5NSwzMDguMzQ0eiIvPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPC9zdmc+DQo='
        );

        add_submenu_page(
            $this->page_name,
            __( 'Configurações', Core::TEXTDOMAIN ),
            __( 'Configurações', Core::TEXTDOMAIN ),
            'manage_options',
            $this->page_name
        );

        if ( $this->is_log_menu ) {
            add_submenu_page(
                $this->page_name,
                __( 'Logs', Core::TEXTDOMAIN ),
                __( 'Logs', Core::TEXTDOMAIN ),
                'manage_options',
                $this->page_log,
                [ $this, 'mod_create_page_log' ]
            );
        }
    }
    /**
     * Options admin page callback
     */
    public function mod_create_admin_page() {
        Settings_View::mod_admin_page_html();
    }
    /**
     * Options log page callback
     */
    public function mod_create_page_log() {
        Settings_View::mod_log_page_html();
    }
    /**
     * Register and add settings
     */
    public function mod_page_init() {
        $this->settings_model->mod_page_register_fields( $this->page_name );
    }
}
