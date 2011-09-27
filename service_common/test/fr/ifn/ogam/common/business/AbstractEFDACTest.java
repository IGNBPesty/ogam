package fr.ifn.ogam.common.business;

import java.io.FileInputStream;
import java.io.IOException;
import java.util.Properties;

import javax.naming.Context;
import javax.naming.NamingException;

import org.apache.log4j.BasicConfigurator;
import org.apache.log4j.ConsoleAppender;
import org.apache.log4j.Layout;
import org.apache.log4j.Level;
import org.apache.log4j.Logger;
import org.apache.log4j.PatternLayout;
import org.dbunit.JndiBasedDBTestCase;
import org.dbunit.dataset.IDataSet;
import org.dbunit.dataset.xml.FlatXmlDataSet;
import org.dbunit.operation.DatabaseOperation;

import fr.ifn.ogam.common.util.JNDIUnitTestHelper;

/**
 * Mother classe for the EFDAC service test classes.
 */
public class AbstractEFDACTest extends JndiBasedDBTestCase {

	protected static Logger logger = null;

	/**
	 * JNDI names of the datasources
	 */
	protected static final String WEBSITE_JNDI_URL = "java:/comp/env/jdbc/website";
	protected static final String RAWDATA_JNDI_URL = "java:/comp/env/jdbc/rawdata";
	protected static final String METADATA_JNDI_URL = "java:/comp/env/jdbc/metadata";
	protected static final String HARMONIZED_JNDI_URL = "java:/comp/env/jdbc/harmonizeddata";

	/**
	 * JNDI connexion pools.
	 */
	protected JNDIUnitTestHelper websiteJNDI;
	protected JNDIUnitTestHelper rawdataJNDI;
	protected JNDIUnitTestHelper metadataJNDI;
	protected JNDIUnitTestHelper harmonizedJNDI;

	/**
	 * Constructor
	 * 
	 * @param name
	 */
	public AbstractEFDACTest(String name) {
		super(name);
	}

	/**
	 * Returns the JNDI properties to use.<br>
	 */
	protected Properties getJNDIProperties() {
		Properties env = new Properties();
		env.put(Context.INITIAL_CONTEXT_FACTORY, JNDIUnitTestHelper.getContextFactoryName());
		return env;
	}

	/**
	 * Access to the metadata database
	 */
	protected String getLookupName() {
		return METADATA_JNDI_URL;
	}

	/**
	 * Locate the data
	 */
	protected IDataSet getDataSet() throws Exception {
		return new FlatXmlDataSet(new FileInputStream("./test/test_metadata.xml"), false);
	}

	/**
	 * Insert the test metadata before to do the tests
	 */
	protected DatabaseOperation getSetUpOperation() throws Exception {
		logger.debug("Preparing the metadata for the test");
		return DatabaseOperation.REFRESH;
	}

	/**
	 * Remove the test metadata after the tests
	 */
	protected DatabaseOperation getTearDownOperation() throws Exception {
		logger.debug("Cleaning the metadata after the test");
		return DatabaseOperation.DELETE;
	}

	/**
	 * Initialise the test session.
	 */
	protected void setUp() throws Exception {
		try {
			// Initialise Log4J
			if (logger == null) {
				logger = Logger.getLogger(this.getClass());

				// Log general
				Layout layout = new PatternLayout("%-5p [%t]: %m%n");
				ConsoleAppender appender = new ConsoleAppender(layout, ConsoleAppender.SYSTEM_OUT);
				BasicConfigurator.configure(appender);
				Logger.getRootLogger().setLevel(Level.TRACE);

				Logger dblogger = Logger.getLogger("org.dbunit");
				dblogger.addAppender(appender);

			}

			// Initialise the connexion pools
			// test-efdac
			// websiteJNDI = new JNDIUnitTestHelper("org.postgresql.Driver", "jdbc:postgresql://test-efdac:5432/efdac", "eforest", "yC50zm9", WEBSITE_JNDI_URL);
			// rawdataJNDI = new JNDIUnitTestHelper("org.postgresql.Driver", "jdbc:postgresql://test-efdac:5432/efdac", "eforest", "yC50zm9", RAWDATA_JNDI_URL);
			// metadataJNDI = new JNDIUnitTestHelper("org.postgresql.Driver", "jdbc:postgresql://test-efdac:5432/efdac", "eforest", "yC50zm9",
			// METADATA_JNDI_URL);
			// harmonizedJNDI = new JNDIUnitTestHelper("org.postgresql.Driver", "jdbc:postgresql://test-efdac:5432/efdac", "eforest", "yC50zm9",
			// HARMONIZED_JNDI_URL);

			// localhost
			websiteJNDI = new JNDIUnitTestHelper("org.postgresql.Driver", "jdbc:postgresql://localhost:5433/ogam", "ogam", "ogam", WEBSITE_JNDI_URL);
			rawdataJNDI = new JNDIUnitTestHelper("org.postgresql.Driver", "jdbc:postgresql://localhost:5433/ogam", "ogam", "ogam", RAWDATA_JNDI_URL);
			metadataJNDI = new JNDIUnitTestHelper("org.postgresql.Driver", "jdbc:postgresql://localhost:5433/ogam", "ogam", "ogam", METADATA_JNDI_URL);
			harmonizedJNDI = new JNDIUnitTestHelper("org.postgresql.Driver", "jdbc:postgresql://localhost:5433/ogam", "ogam", "ogam", HARMONIZED_JNDI_URL);

		} catch (IOException ioe) {
			ioe.printStackTrace();
			fail("IOException thrown : " + ioe.getMessage());
		} catch (NamingException ne) {
			ne.printStackTrace();
			fail("NamingException thrown on Init : " + ne.getMessage());
		}

		// Call the DBUnit setup method
		super.setUp();

	}

	/**
	 * Clean the test session.
	 */
	protected void tearDown() throws Exception {
		try {

			// Call the DBUnit teardown method
			super.tearDown();

			// Shutdown les pools de connexion JNDI
			websiteJNDI.shutdown();
			rawdataJNDI.shutdown();
			metadataJNDI.shutdown();

		} catch (NamingException ne) {
			ne.printStackTrace();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

}
