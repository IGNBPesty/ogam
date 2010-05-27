package fr.ifn.eforest.integration.database.rawdata;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Date;

import javax.naming.Context;
import javax.naming.InitialContext;
import javax.naming.NamingException;
import javax.sql.DataSource;

import org.apache.log4j.Logger;

import fr.ifn.eforest.integration.business.submissions.SubmissionStep;

/**
 * Data Access Object used to access the application parameters.
 */
public class SubmissionDAO {

	private Logger logger = Logger.getLogger(this.getClass());

	/**
	 * Get the next submission id.
	 */
	private static final String GET_NEXT_SUBMISSION_ID_STMT = "SELECT nextval('submission_id_seq') as submissionid";

	/**
	 * Create a new submission.
	 */
	private static final String CREATE_SUBMISSION_STMT = "INSERT INTO submission (submission_id, type, country_code, step) values (?, ?, ?, ?)";

	/**
	 * update the submission step and status.
	 */
	private static final String UPDATE_SUBMISSION_STATUS_STMT = "UPDATE submission SET status = ?, STEP = ? WHERE submission_id = ?";

	/**
	 * validate the submission.
	 */
	private static final String VALIDATE_SUBMISSION_STMT = "UPDATE submission SET STEP = ?, _validationdt = ? WHERE submission_id = ?";

	/**
	 * update the submission step.
	 */
	private static final String UPDATE_SUBMISSION_STEP_STMT = "UPDATE submission SET STEP = ? WHERE submission_id = ?";

	/**
	 * Insert information about a submission file.
	 */
	private static final String INSERT_SUBMISSION_FILE_STMT = "INSERT INTO submission_file (submission_id, file_type, file_name, nb_line) values (?, ?, ?, ?)";

	/**
	 * Get a submission.
	 */
	private static final String GET_SUBMISSION_BY_ID_STMT = "SELECT submission_id, type, step, status, country_code FROM submission WHERE submission_id = ?";

	/**
	 * Get a connexion to the database.
	 * 
	 * @return The <code>Connection</code>
	 * @throws NamingException
	 * @throws SQLException
	 */
	private Connection getConnection() throws NamingException, SQLException {

		Context initContext = new InitialContext();
		DataSource ds = (DataSource) initContext.lookup("java:/comp/env/jdbc/rawdata");
		Connection cx = ds.getConnection();

		return cx;
	}

	/**
	 * Get a submission information.
	 * 
	 * @param submissionId
	 *            the identifier of the submission
	 * @return the submission
	 */
	public SubmissionData getSubmission(Integer submissionId) throws Exception {

		Connection con = null;
		PreparedStatement ps = null;
		ResultSet rs = null;
		SubmissionData result = null;
		try {

			con = getConnection();

			// Get the submission ID from the sequence
			ps = con.prepareStatement(GET_SUBMISSION_BY_ID_STMT);
			ps.setInt(1, submissionId);

			logger.trace(GET_SUBMISSION_BY_ID_STMT);
			rs = ps.executeQuery();

			if (rs.next()) {
				result = new SubmissionData();
				result.setSubmissionId(submissionId);
				result.setStep(rs.getString("step"));
				result.setStatus(rs.getString("status"));
				result.setType(rs.getString("type"));
				result.setCountryCode(rs.getString("country_code"));
			}

			return result;
		} finally {
			try {
				if (rs != null) {
					rs.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing resultset : " + e.getMessage());
			}
			try {
				if (ps != null) {
					ps.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing statement : " + e.getMessage());
			}
			try {
				if (con != null) {
					con.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing connexion : " + e.getMessage());
			}
		}
	}

	/**
	 * Create a new submission.
	 * 
	 * @param submissionType
	 *            the submission type
	 * @param countryCode
	 *            the code of the country
	 * @return the identifier of the new submission
	 */
	public Integer newSubmission(String submissionType, String countryCode) throws Exception {

		Connection con = null;
		PreparedStatement ps = null;
		ResultSet rs = null;
		Integer submissionId = null;
		try {

			con = getConnection();

			// Get the submission ID from the sequence
			ps = con.prepareStatement(GET_NEXT_SUBMISSION_ID_STMT);
			logger.trace(GET_NEXT_SUBMISSION_ID_STMT);
			rs = ps.executeQuery();

			rs.next();
			submissionId = rs.getInt("submissionid");

			// close the previous statement
			if (ps != null) {
				ps.close();
			}

			// Insert the submission in the table
			// Preparation of the request
			ps = con.prepareStatement(CREATE_SUBMISSION_STMT);
			logger.trace(CREATE_SUBMISSION_STMT);
			ps.setInt(1, submissionId);
			ps.setString(2, submissionType);
			ps.setString(3, countryCode);
			ps.setString(4, SubmissionStep.INITIALISED);
			ps.execute();

			return submissionId;

		} finally {
			try {
				if (rs != null) {
					rs.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing resultset : " + e.getMessage());
			}
			try {
				if (ps != null) {
					ps.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing statement : " + e.getMessage());
			}
			try {
				if (con != null) {
					con.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing connexion : " + e.getMessage());
			}
		}
	}

	/**
	 * Update the submission step and status.
	 * 
	 * @param submissionId
	 *            the identifier of the submission
	 * @param step
	 *            the step of the submission
	 * @param status
	 *            the status of the submission
	 */
	public void updateSubmissionStatus(Integer submissionId, String step, String status) throws Exception {

		Connection con = null;
		PreparedStatement ps = null;
		try {

			con = getConnection();

			// Get the submission ID from the sequence
			ps = con.prepareStatement(UPDATE_SUBMISSION_STATUS_STMT);
			logger.trace(UPDATE_SUBMISSION_STATUS_STMT);
			ps.setString(1, status);
			ps.setString(2, step);
			ps.setInt(3, submissionId);
			ps.execute();

		} finally {
			try {
				if (ps != null) {
					ps.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing statement : " + e.getMessage());
			}
			try {
				if (con != null) {
					con.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing connexion : " + e.getMessage());
			}
		}
	}

	/**
	 * Update the submission step (don't change the status).
	 * 
	 * @param submissionId
	 *            the identifier of the submission
	 * @param step
	 *            the step of the submission
	 */
	public void updateSubmissionStep(Integer submissionId, String step) throws Exception {

		Connection con = null;
		PreparedStatement ps = null;
		try {

			con = getConnection();

			// Get the submission ID from the sequence
			ps = con.prepareStatement(UPDATE_SUBMISSION_STEP_STMT);
			logger.trace(UPDATE_SUBMISSION_STEP_STMT);
			ps.setString(1, step);
			ps.setInt(2, submissionId);
			ps.execute();

		} finally {
			try {
				if (ps != null) {
					ps.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing statement : " + e.getMessage());
			}
			try {
				if (con != null) {
					con.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing connexion : " + e.getMessage());
			}
		}
	}

	/**
	 * Validate the submission.
	 * 
	 * @param submissionId
	 *            the identifier of the submission
	 */
	public void validateSubmission(Integer submissionId) throws Exception {

		Connection con = null;
		PreparedStatement ps = null;
		try {

			con = getConnection();

			// Get the submission ID from the sequence
			ps = con.prepareStatement(VALIDATE_SUBMISSION_STMT);
			logger.trace(VALIDATE_SUBMISSION_STMT);
			ps.setString(1, SubmissionStep.DATA_VALIDATED);
			ps.setTimestamp(2, new java.sql.Timestamp(new Date().getTime()));
			ps.setInt(3, submissionId);
			ps.execute();

		} finally {
			try {
				if (ps != null) {
					ps.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing statement : " + e.getMessage());
			}
			try {
				if (con != null) {
					con.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing connexion : " + e.getMessage());
			}
		}
	}

	/**
	 * Add information about one file of the submission.
	 * 
	 * @param submissionId
	 *            the identifier of the submission
	 * @param fileType
	 *            the type of the file
	 * @param fileName
	 *            the name of the file
	 * @param lineNumber
	 *            the number of lines of data in the file
	 */
	public void addSubmissionFile(Integer submissionId, String fileType, String fileName, Integer lineNumber) throws Exception {

		Connection con = null;
		PreparedStatement ps = null;
		try {

			con = getConnection();

			// Get the submission ID from the sequence
			ps = con.prepareStatement(INSERT_SUBMISSION_FILE_STMT);
			logger.trace(INSERT_SUBMISSION_FILE_STMT);
			ps.setInt(1, submissionId);
			ps.setString(2, fileType);
			ps.setString(3, fileName);
			ps.setInt(4, lineNumber);
			ps.execute();

		} finally {
			try {
				if (ps != null) {
					ps.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing statement : " + e.getMessage());
			}
			try {
				if (con != null) {
					con.close();
				}
			} catch (SQLException e) {
				logger.error("Error while closing connexion : " + e.getMessage());
			}
		}
	}

}
