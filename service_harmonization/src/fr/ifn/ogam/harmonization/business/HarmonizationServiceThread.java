/**
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 * 
 * © European Union, 2008-2012
 *
 * Reuse is authorised, provided the source is acknowledged. The reuse policy of the European Commission is implemented by a Decision of 12 December 2011.
 *
 * The general principle of reuse can be subject to conditions which may be specified in individual copyright notices. 
 * Therefore users are advised to refer to the copyright notices of the individual websites maintained under Europa and of the individual documents. 
 * Reuse is not applicable to documents subject to intellectual property rights of third parties.
 */
package fr.ifn.ogam.harmonization.business;

import java.util.Date;

import org.apache.log4j.Logger;

import fr.ifn.ogam.common.business.AbstractThread;
import fr.ifn.ogam.common.business.ThreadLock;

/**
 * Thread running the harmonization process.
 */
public class HarmonizationServiceThread extends AbstractThread {

	//
	// The Thread is always linked to a country code and a dataset.
	//
	private String datasetId;
	private String providerId;
	private boolean removeOnly;
	private Integer srid;

	/**
	 * The logger used to log the errors or several information.
	 * 
	 * @see org.apache.log4j.Logger
	 */
	protected final transient Logger logger = Logger.getLogger(this.getClass());

	/**
	 * Constructor.
	 * 
	 * @param datasetId
	 *            the dataset identifier
	 * @param providerId
	 *            the country code
	 * @param removeOnly
	 *            Indicate if we only want to remove data
	 * @param userSrid
	 *            The SRID
	 * @throws Exception
	 */
	public HarmonizationServiceThread(String datasetId, String providerId, boolean removeOnly, Integer srid) throws Exception {

		this.datasetId = datasetId;
		this.providerId = providerId;
		this.removeOnly = removeOnly;
		this.srid = srid;

	}

	/**
	 * Launch in thread mode the harmonization process.
	 */
	public void run() {

		try {

			Date startDate = new Date();
			logger.debug("Start of the harmonization process " + startDate + ".");

			HarmonizationService harmonizationService = new HarmonizationService(this);

			if (removeOnly) {

				// Remove harmonized data
				harmonizationService.removeHarmonizedData(datasetId, providerId);

			} else {

				// Harmonize data
				harmonizationService.harmonizeData(datasetId, providerId, srid);

			}

			// Log the end the the request
			Date endDate = new Date();
			logger.debug("Harmonization process terminated successfully in " + (endDate.getTime() - startDate.getTime()) / 1000.00 + " sec.");

		} finally {
			// Remove itself from the list of running checks
			String key = datasetId + "_" + providerId;
			ThreadLock.getInstance().releaseProcess(key);

		}

	}

}
