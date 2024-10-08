<?php

class WPML_PO_Import_Strings {

	const NONCE_NAME = 'wpml-po-import-strings';

	private $errors;

	public function maybe_import_po_add_strings() {
		if ( array_key_exists( 'icl_po_upload', $_POST ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'icl_po_form' ) ) {
			add_filter( 'wpml_st_get_po_importer', array( $this, 'import_po' ) );
			return;
		}

		if ( array_key_exists( 'action', $_POST ) && 'icl_st_save_strings' === $_POST['action'] && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'add_po_strings' ) ) {
			$this->add_strings();
		}
	}

	/**
	 * @return null|WPML_PO_Import
	 */
	public function import_po() {
		if ( $_FILES[ 'icl_po_file' ][ 'size' ] === 0 ) {
			$this->errors = esc_html__( 'File upload error', 'wpml-string-translation' );
			return null;
		} else {
			$po_importer  = new WPML_PO_Import( $_FILES[ 'icl_po_file' ][ 'tmp_name' ] );
			$this->errors = $po_importer->get_errors();
			return $po_importer;
		}
	}

	/**
	 * @return string
	 */
	public function get_errors() {
		return $this->errors;
	}

	private function add_strings() {
		$strings = json_decode( $_POST['strings_json'] );

		foreach ( $strings as $k => $string ) {
			$original = wp_kses_post( $string->original );
			$context  = (string) \WPML\API\Sanitize::string( $string->context );

			$string->original = str_replace( '\n', "\n", $original );
			$name             = isset( $string->name )
				? (string) \WPML\API\Sanitize::string( $string->name ) : md5( $original );

			$string_id = icl_register_string( array(
				'domain'  => (string) \WPML\API\Sanitize::string( $_POST['icl_st_domain_name']),
				'context' => $context
			),
				$name,
				$original
			);

			$this->maybe_add_translation( $string_id, $string );
		}
	}

	/**
	 * @param int|false|null $string_id
	 * @param \stdClass      $string
	 */
	private function maybe_add_translation( $string_id, $string ) {
		if ( $string_id && array_key_exists( 'icl_st_po_language', $_POST ) ) {
			if ( $string->translation !== '' ) {
				$status = ICL_TM_COMPLETE;
				if ( $string->fuzzy ) {
					$status = ICL_TM_NOT_TRANSLATED;
				}
				$translation = str_replace( '\n', "\n", wp_kses_post( $string->translation ) );

				icl_add_string_translation( $string_id, $_POST[ 'icl_st_po_language' ], $translation, $status );
				icl_update_string_status( $string_id );
			}
		}
	}
}
