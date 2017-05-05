/**
 * Created by Peter on 30.03.2015.
 */

/**
 * Deletes a parent <tr> from table
 *
 * @param e
 */
function delRow(e)
{
	e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode);
}
