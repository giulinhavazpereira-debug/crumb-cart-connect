const Index = () => {
  return (
    <div className="min-h-screen bg-background p-8">
      <div className="max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold mb-2 text-foreground">🧁 Bakery Orders — WordPress Plugin</h1>
        <p className="text-muted-foreground mb-6">Your plugin files are ready in <code className="bg-muted px-2 py-1 rounded text-sm">public/wordpress-plugin/bakery-orders/</code></p>

        <div className="space-y-6">
          <Section title="📦 Installation">
            <ol className="list-decimal pl-5 space-y-1 text-foreground">
              <li>Download the <code className="bg-muted px-1 rounded text-sm">bakery-orders</code> folder</li>
              <li>Upload it to <code className="bg-muted px-1 rounded text-sm">/wp-content/plugins/</code></li>
              <li>Activate the plugin in WordPress Admin → Plugins</li>
            </ol>
          </Section>

          <Section title="🏷️ Shortcodes (Elementor Compatible)">
            <table className="w-full text-sm border border-border rounded-lg overflow-hidden">
              <thead className="bg-muted">
                <tr>
                  <th className="text-left p-3 text-foreground">Shortcode</th>
                  <th className="text-left p-3 text-foreground">Description</th>
                </tr>
              </thead>
              <tbody>
                {[
                  ['[bakery_menu]', 'Product menu with category filters'],
                  ['[bakery_menu category="cakes"]', 'Filtered by category'],
                  ['[bakery_cart]', 'Shopping cart'],
                  ['[bakery_checkout]', 'Checkout form'],
                ].map(([code, desc], i) => (
                  <tr key={i} className="border-t border-border">
                    <td className="p-3 font-mono text-xs text-foreground">{code}</td>
                    <td className="p-3 text-muted-foreground">{desc}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </Section>

          <Section title="📁 Plugin Structure">
            <pre className="bg-muted p-4 rounded-lg text-xs text-foreground overflow-x-auto">{`bakery-orders/
├── bakery-orders.php          # Main plugin file
├── includes/
│   ├── post-types.php         # Product custom post type
│   ├── cart.php               # Cart session logic
│   ├── checkout.php           # Order processing
│   ├── shortcodes.php         # Elementor shortcodes
│   ├── admin.php              # Admin orders panel
│   └── ajax.php               # AJAX handlers
└── assets/
    ├── css/
    │   ├── bakery-orders.css  # Frontend styles
    │   └── bakery-admin.css   # Admin styles
    └── js/
        ├── bakery-orders.js   # Frontend JS
        └── bakery-admin.js    # Admin JS`}</pre>
          </Section>

          <Section title="✨ Features">
            <ul className="list-disc pl-5 space-y-1 text-muted-foreground">
              <li>Product menu with category filters (cakes, brownies, cookies, cupcakes)</li>
              <li>AJAX-powered cart with add/remove/quantity updates</li>
              <li>Checkout form with order storage in database</li>
              <li>Admin dashboard with order status management</li>
              <li>Responsive pastel design</li>
              <li>WooCommerce compatible (non-conflicting)</li>
            </ul>
          </Section>
        </div>
      </div>
    </div>
  );
};

const Section = ({ title, children }: { title: string; children: React.ReactNode }) => (
  <div className="bg-card border border-border rounded-xl p-6 shadow-sm">
    <h2 className="text-lg font-semibold mb-3 text-foreground">{title}</h2>
    {children}
  </div>
);

export default Index;
