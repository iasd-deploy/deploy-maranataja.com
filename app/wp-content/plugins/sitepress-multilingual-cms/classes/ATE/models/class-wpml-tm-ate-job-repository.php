<?php

use function WPML\FP\invoke;
use WPML\TM\ATE\Jobs;

class WPML_TM_ATE_Job_Repository {

	/** @var WPML_TM_Jobs_Repository */
	private $job_repository;

	/** @var Jobs */
	private $ateJobs;

	public function __construct( WPML_TM_Jobs_Repository $job_repository, Jobs $ateJobs ) {
		$this->job_repository  = $job_repository;
		$this->ateJobs         = $ateJobs;
	}

	/**
	 * If $onlyIds is true, it will return an array of ids instead of a collection of jobs. It is more optimized query which is used in the sync process to avoid loading all the jobs.
	 *
	 * @param bool $includeManualAndLongstandingJobs
	 *
	 * @return WPML_TM_Jobs_Collection|int[]
	 */
	public function get_jobs_to_sync( $includeManualAndLongstandingJobs = true, $onlyIds = false ) {
		if ( $onlyIds ) {
			return $this->ateJobs->getATEJobIdsToSync( $includeManualAndLongstandingJobs );
		}

		$searchParams = $this->getSearchParamsPrototype();
		$searchParams->set_status( [ ICL_TM_WAITING_FOR_TRANSLATOR, ICL_TM_IN_PROGRESS ] );

		$searchParams->set_exclude_manual( ! $includeManualAndLongstandingJobs );
		$searchParams->set_exclude_longstanding( ! $includeManualAndLongstandingJobs );

		return $this->job_repository
			->get( $searchParams )
			->filter( invoke( 'is_ate_job' ) );
	}

	/**
	 * @param array $ateJobIds
	 *
	 * @return bool
	 */
	public function increment_ate_sync_count( array $ateJobIds ) {
		return $this->job_repository->increment_ate_sync_count( $ateJobIds );
	}

	/**
	 * @return WPML_TM_Jobs_Collection
	 */
	public function get_jobs_to_retry() {
		$searchParams = $this->getSearchParamsPrototype();
		$searchParams->set_status( [ ICL_TM_ATE_NEEDS_RETRY ] );

		return $this->job_repository
			->get( $searchParams )
			->filter( invoke( 'is_ate_job' ) );
	}

	/**
	 * @return WPML_TM_Jobs_Search_Params
	 */
	private function getSearchParamsPrototype() {
		$searchParams = new WPML_TM_Jobs_Search_Params();
		$searchParams->set_scope( WPML_TM_Jobs_Search_Params::SCOPE_LOCAL );
		$searchParams->set_job_types( [
			WPML_TM_Job_Entity::POST_TYPE,
			WPML_TM_Job_Entity::PACKAGE_TYPE,
			WPML_TM_Job_Entity::STRING_BATCH,
		] );

		return $searchParams;
	}
}
