package fr.ifn.ogam.harmonization.servlet;

import java.io.IOException;

import javax.servlet.ServletOutputStream;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.log4j.Logger;

import fr.ifn.ogam.common.servlet.AbstractServlet;
import fr.ifn.ogam.common.business.ThreadLock;
import fr.ifn.ogam.harmonization.business.HarmonizationService;
import fr.ifn.ogam.harmonization.business.HarmonizationServiceThread;
import fr.ifn.ogam.harmonization.business.HarmonizationStatus;

/**
 * Harmonization Servlet. <br>
 * <br>
 * Copy the raw data and process harmonization.
 */
public class HarmonizationServlet extends AbstractServlet {

	/**
	 * The logger used to log the errors or several information.
	 * 
	 * @see org.apache.log4j.Logger
	 */
	private final transient Logger logger = Logger.getLogger(this.getClass());

	/**
	 * The serial version ID used to identify the object.
	 */
	private static final long serialVersionUID = -455284792196591246L;

	/**
	 * Input parameters.
	 */
	private static final String ACTION = "action";
	private static final String ACTION_HARMONIZE = "HarmonizeData";
	private static final String ACTION_REMOVE_HARMONIZE_DATA = "RemoveHarmonizeData";
	private static final String ACTION_STATUS = "status";

	private static final String DATASET_ID = "DATASET_ID";
	private static final String PROVIDER_ID = "PROVIDER_ID";

	// Service
	private transient HarmonizationService harmonizationService = new HarmonizationService();

	/**
	 * Main function of the servlet.
	 * 
	 * @param request
	 *            the request done to the servlet
	 * @param response
	 *            the response sent
	 */
	public void service(HttpServletRequest request, HttpServletResponse response) throws IOException {

		response.setContentType("text/xml");
		response.setCharacterEncoding("UTF-8");

		String action = null;
		ServletOutputStream out = response.getOutputStream();

		logRequestParameters(request);

		try {

			logger.debug("Harmonization Servlet called");

			action = request.getParameter(ACTION);
			if (action == null) {
				throw new Exception("The " + ACTION + " parameter is mandatory");
			}

			String datasetId = request.getParameter(DATASET_ID);
			if (datasetId == null) {
				throw new Exception("The " + DATASET_ID + " parameter is mandatory");
			}

			String providerId = request.getParameter(PROVIDER_ID);
			if (providerId == null) {
				throw new Exception("The " + PROVIDER_ID + " parameter is mandatory");
			}

			// Identifier of the process
			String key = datasetId + "_" + providerId;

			/*
			 * Get the STATE of the process for a submission
			 */
			if (action.equals(ACTION_STATUS)) {

				// Try to get the instance of the checkservice for this submissionId
				HarmonizationServiceThread process = (HarmonizationServiceThread) ThreadLock.getInstance().getProcess(key);

				if (process != null) {
					// There is a running thread, we get its current status.
					out.print(generateResult(HarmonizationStatus.RUNNING, process));
				} else {
					// We try to get the status of the last harmonization
					out.print(generateResult(harmonizationService.getHarmonizationStatus(datasetId, providerId)));
				}

			} else

			/*
			 * Launch the harmonization of data
			 */
			if (action.equals(ACTION_HARMONIZE) || action.equals(ACTION_REMOVE_HARMONIZE_DATA)) {

				// Check if a thread is already running
				HarmonizationServiceThread process = (HarmonizationServiceThread) ThreadLock.getInstance().getProcess(key);
				if (process != null) {
					throw new Exception("A process is already running for this country and dataset");
				}

				// Launch the harmonization thread
				boolean removeOnly = false;
				if(action.equals(ACTION_REMOVE_HARMONIZE_DATA)){
					removeOnly = true;
				}
				process = new HarmonizationServiceThread(datasetId, providerId, removeOnly);
				process.start();

				// Register the running thread
				ThreadLock.getInstance().lockProcess(key, process);

				// Output the current status of the check service
				out.print(generateResult(HarmonizationStatus.RUNNING, process));

			} else {
				throw new Exception("The action type is unknown, should be " + ACTION_HARMONIZE);
			}

		} catch (Exception e) {
			logger.error("Error during data harmonization", e);
			out.print(generateErrorMessage(e.getMessage()));
		}
	}

}
