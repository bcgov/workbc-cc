/*====================================================================================================================================*
 * WorkBC Quizzes Apps Script.
 *
 * Deployment procedure:
 *
 * - Open your Google Sheet > Extensions > Apps Script
 * - Add the current file into the "Files" section and name it "WorkBCQuizzes.gs"
 * - Add the file https://github.com/bradjasper/ImportJSON/blob/master/ImportJSON.gs verbatim into the "Files" section
 * - In Project Settings > Script Properties, add properties ONET_USERNAME and ONET_PASSWORD with appropriate O*NET credentials (as found on CDQ at /admin/content/site-settings)
 * - In your sheet, insert the following formula in a cell:
 * =MatchOnetCareers("432104444400000222220123443210333334321044444432104444400000")
 * or
 * =MatchOnetCareers(CONCATENATE(Interests!M5:M64))
 *====================================================================================================================================*/

/**
 * Retrieves matching careers from O*NET My Next Move API.
 */
function MatchOnetCareers(answers) {
  if (typeof answers != "string" || answers.length != 60) {
    throw "[O*NET Career Match] Expecting 60 answers.";
  }
  const username = PropertiesService.getScriptProperties().getProperty('ONET_USERNAME');
  const password = PropertiesService.getScriptProperties().getProperty('ONET_PASSWORD');
  var fetchOptions = {
    headers: {
      Accept: "application/json",
      Authorization: "Basic " + Utilities.base64Encode(username + ":" + password),
    }
  };
  const answers_onet = answers.split('').map((char) => { return parseInt(char)+1; }).join('');
  return ImportJSONAdvanced("https://services.onetcenter.org/ws/mnm/interestprofiler/careers?end=1000&answers=" + answers_onet, fetchOptions, "/career/title,/career/code,/career/fit", "noHeaders", includeXPath_, defaultTransform_);
}
