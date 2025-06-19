import { PluginSidebar } from "@wordpress/editor";
import { PanelBody } from "@wordpress/components";
import { registerPlugin } from "@wordpress/plugins";
import { blockMeta } from "@wordpress/icons";
import { useSelect } from "@wordpress/data";
import { __ } from "@wordpress/i18n";
import "../css/block-editor.css";

const RevelioSidebar = () => {
  const meta = useSelect(
    (select) => select("core/editor").getEditedPostAttribute("revelio-meta"),
    []
  );

  return (
    <PluginSidebar
      name="revelio-sidebar"
      title={__("Revelio Post Meta", "revelio")}
      className="revelio-sidebar-wrapper"
    >
      <PanelBody>
        {!meta || Object.keys(meta).length === 0 ? (
          <p>{__("No post meta found.", "revelio")}</p>
        ) : (
          <table className="revelio-post-meta-table">
            <tbody>
              {Object.entries(meta).map(([key, values]) => (
                <tr key={key}>
                  <th scope="row">
                    <code>{key}</code>
                  </th>
                  <td>
                    {(Array.isArray(values) ? values : [values]).map(
                      (val, i) => (
                        <p key={i}>{val}</p>
                      )
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </PanelBody>
    </PluginSidebar>
  );
};

registerPlugin("revelio", {
  icon: blockMeta,
  render: RevelioSidebar,
});
