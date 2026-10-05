import { useEffect, useState } from "react";

const configuredApiUrl = (import.meta.env.VITE_API_URL || "").replace(/\/+$/, "");
const API_URL = import.meta.env.DEV
  ? ""
  : configuredApiUrl.replace(/\/api$/i, "");
const FALLBACK_API_URLS = [API_URL];

const buildApiUrl = (baseUrl, endpoint) => {
  if (!endpoint) {
    return baseUrl || "";
  }

  if (endpoint.startsWith("http")) {
    return endpoint;
  }

  const cleanBase = baseUrl ? baseUrl.replace(/\/$/, "") : "";
  const cleanEndpoint = endpoint.startsWith("/") ? endpoint : `/${endpoint}`;

  return `${cleanBase}${cleanEndpoint}`;
};

const parseApiResult = async (response) => {
  const contentType = response.headers.get("content-type") || "";
  const rawText = await response.text();

  if (!rawText) {
    return {
      message: "Request failed. Please try again.",
    };
  }

  if (contentType.includes("application/json") || rawText.trim().startsWith("{")) {
    try {
      const data = JSON.parse(rawText);
      return {
        ...data,
        message: data.message || data.error,
      };
    } catch {
      return { message: rawText.trim() };
    }
  }

  const errorDocument = new DOMParser().parseFromString(rawText, "text/html");
  errorDocument.querySelectorAll("style, script").forEach((element) => element.remove());
  const cleanText = (errorDocument.body.textContent || "")
    .replace(/\s+/g, " ")
    .trim();

  return { message: cleanText || "Request failed. Please try again." };
};

const requestWithFallback = async (endpoints, payload, method = "POST") => {
  let lastError = null;

  for (const baseUrl of FALLBACK_API_URLS) {
    for (const endpoint of endpoints) {
      const url = buildApiUrl(baseUrl, endpoint);

      try {
        const response = await fetch(url, {
          method,
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: method === "GET" ? undefined : JSON.stringify(payload || {}),
        });

        const data = await parseApiResult(response);

        if (response.ok) {
          return { ok: true, data };
        }

        lastError = data;
      } catch {
        lastError = { message: "Unable to connect to the API." };
      }
    }
  }

  return { ok: false, data: lastError || { message: "Request failed. Please try again." } };
};

function App() {
  // ==========================================
  // LOGIN
  // ==========================================

  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [registerUsername, setRegisterUsername] = useState("");
  const [registerEmail, setRegisterEmail] = useState("");
  const [registerPassword, setRegisterPassword] = useState("");
  const [authMode, setAuthMode] = useState("login");

  const [user, setUser] = useState(() => {
    const savedUser = localStorage.getItem("user");

    try {
      return savedUser ? JSON.parse(savedUser) : null;
    } catch {
      return null;
    }
  });

  const [message, setMessage] = useState("");
  const [isRegistrationSuccess, setIsRegistrationSuccess] = useState(false);

  // Login success notification
  const [loginSuccess, setLoginSuccess] = useState(false);

  // ==========================================
  // PRODUCTS
  // ==========================================

  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(false);

  // ==========================================
  // PRODUCT FORM
  // ==========================================

  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState(null);

  const [productName, setProductName] = useState("");
  const [description, setDescription] = useState("");
  const [price, setPrice] = useState("");
  const [quantity, setQuantity] = useState("");

  // ==========================================
  // GET TOKEN
  // ==========================================

  const getToken = () => {
    return localStorage.getItem("access_token");
  };

  // ==========================================
  // LOGIN
  // ==========================================

  const login = async (e) => {
    e.preventDefault();

    setIsRegistrationSuccess(false);
    setMessage("");

    try {
      const result = await requestWithFallback([
        "/api/login",
      ], { username, password }, "POST");

      if (!result.ok) {
        setMessage(result.data.message || "Incorrect Information");
        return;
      }

      const data = result.data;

      // Save access token
      localStorage.setItem(
        "access_token",
        data.tokens.access_token
      );

      // Save user information
      localStorage.setItem(
        "user",
        JSON.stringify(data.user)
      );

      // Set logged-in user
      setUser(data.user);

      // Clear login fields
      setUsername("");
      setPassword("");

      // Clear normal message
      setMessage("");

      // Show success notification
      setLoginSuccess(true);

      // Hide notification after 4 seconds
      setTimeout(() => {
        setLoginSuccess(false);
      }, 4000);

      // Load products
      loadProducts(data.tokens.access_token);

    } catch (error) {
      console.error(error);
      setMessage("Unable to connect to the API.");
    }
  };

  const register = async (e) => {
    e.preventDefault();

    setIsRegistrationSuccess(false);
    setMessage("");

    if (!registerUsername.trim() || !registerEmail.trim() || !registerPassword.trim()) {
      setMessage("Please complete all fields.");
      return;
    }

    try {
      const result = await requestWithFallback([
        "/api/register",
      ], {
        username: registerUsername.trim(),
        email: registerEmail.trim(),
        password: registerPassword,
      }, "POST");

      if (!result.ok) {
        setMessage(result.data.message || "Registration failed. Please try again.");
        return;
      }

      setIsRegistrationSuccess(true);
      setMessage(result.data.message || "Account Successfully Created");
      setRegisterUsername("");
      setRegisterEmail("");
      setRegisterPassword("");
      setAuthMode("login");
      setUsername("");
      setPassword("");
    } catch (error) {
      console.error(error);
      setMessage("Registration failed. Please try again.");
    }
  };

  // ==========================================
  // LOGOUT
  // ==========================================

  const logout = () => {
    localStorage.removeItem("access_token");
    localStorage.removeItem("user");

    setUser(null);
    setProducts([]);

    setLoginSuccess(false);
    setMessage("");

    closeForm();
  };

  // ==========================================
  // LOAD PRODUCTS
  // ==========================================

  const loadProducts = async (token = getToken()) => {
    if (!token) {
      return;
    }

    setLoading(true);
    setMessage("");

    try {
      const response = await fetch(buildApiUrl(API_URL, "/api/products"), {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      const data = await parseApiResult(response);

      if (!response.ok) {
        setMessage(
          data.message || "Failed to load products."
        );
        return;
      }

      setProducts(data.products || []);

    } catch (error) {
      console.error(error);
      setMessage("Unable to load products.");
    } finally {
      setLoading(false);
    }
  };

  // ==========================================
  // LOAD PRODUCTS IF ALREADY LOGGED IN
  // ==========================================

  useEffect(() => {
    if (user) {
      loadProducts();
    }
  }, []);

  // ==========================================
  // OPEN ADD FORM
  // AUTHENTICATED USERS
  // ==========================================

  const openAddForm = () => {
    setEditingId(null);

    setProductName("");
    setDescription("");
    setPrice("");
    setQuantity("");

    setMessage("");

    setShowForm(true);
  };

  // ==========================================
  // OPEN EDIT FORM
  // AUTHENTICATED USERS
  // ==========================================

  const openEditForm = (product) => {
    setEditingId(product.id);

    setProductName(product.product_name || "");
    setDescription(product.description || "");
    setPrice(product.price || "");
    setQuantity(product.quantity || "");

    setMessage("");

    setShowForm(true);
  };

  // ==========================================
  // CLOSE FORM
  // ==========================================

  const closeForm = () => {
    setShowForm(false);

    setEditingId(null);

    setProductName("");
    setDescription("");
    setPrice("");
    setQuantity("");
  };

  // ==========================================
  // SAVE PRODUCT
  // ADD / UPDATE
  // AUTHENTICATED USERS
  // ==========================================

  const saveProduct = async (e) => {
    e.preventDefault();

    const token = getToken();

    if (!token) {
      setMessage("Please login first.");
      return;
    }

    // Validation
    if (!productName.trim()) {
      setMessage("Product name is required.");
      return;
    }

    if (!description.trim()) {
      setMessage("Description is required.");
      return;
    }

    if (price === "" || Number(price) < 0) {
      setMessage("Please enter a valid price.");
      return;
    }

    if (quantity === "" || Number(quantity) < 0) {
      setMessage("Please enter a valid quantity.");
      return;
    }

    try {
      const isEditing = editingId !== null;

      const url = isEditing
        ? buildApiUrl(API_URL, `/api/products/${editingId}`)
        : buildApiUrl(API_URL, "/api/products");

      const method = isEditing ? "PUT" : "POST";

      const response = await fetch(url, {
        method,
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          product_name: productName.trim(),
          description: description.trim(),
          price: Number(price),
          quantity: Number(quantity),
        }),
      });

      const data = await parseApiResult(response);

      if (!response.ok) {
        setMessage(
          data.message || "Unable to save product."
        );
        return;
      }

      setMessage(
        isEditing
          ? "Product updated successfully!"
          : "Product added successfully!"
      );

      closeForm();

      await loadProducts();

    } catch (error) {
      console.error(error);
      setMessage("Unable to connect to the API.");
    }
  };

  // ==========================================
  // DELETE PRODUCT
  // AUTHENTICATED USERS
  // ==========================================

  const deleteProduct = async (id) => {
    const confirmed = window.confirm(
      "Are you sure you want to delete this product?"
    );

    if (!confirmed) {
      return;
    }

    const token = getToken();

    if (!token) {
      setMessage("Please login first.");
      return;
    }

    try {
      const response = await fetch(
        buildApiUrl(API_URL, `/api/products/${id}`),
        {
          method: "DELETE",
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        }
      );

      const data = await parseApiResult(response);

      if (!response.ok) {
        setMessage(
          data.message || "Unable to delete product."
        );
        return;
      }

      setMessage("Product deleted successfully!");

      await loadProducts();

    } catch (error) {
      console.error(error);
      setMessage("Unable to connect to the API.");
    }
  };

  // ==========================================
  // LOGIN SCREEN
  // ==========================================

  if (!user) {
    return (
      <div style={styles.loginPage}>

        <div style={styles.loginCard}>

          <h1 style={styles.loginTitle}>
            {authMode === "login" ? "Login" : "Register"}
          </h1>

          {message && (
            <div style={isRegistrationSuccess ? styles.flashSuccess : styles.flashError}>
              <span style={isRegistrationSuccess ? styles.successIcon : styles.errorIcon}>
                {isRegistrationSuccess ? "✓" : "×"}
              </span>
              {message}
            </div>
          )}

          {authMode === "login" ? (
            <form onSubmit={login} style={styles.form}>

              <input
                type="text"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                placeholder="Username"
                style={styles.input}
                required
              />

              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Password"
                style={styles.input}
                required
              />

              <button
                type="submit"
                style={styles.primaryButton}
              >
                Sign In
              </button>

              <div style={styles.switchRow}>
                <span style={styles.switchText}>Need an account?</span>
                <button
                  type="button"
                  onClick={() => {
                    setAuthMode("register");
                    setMessage("");
                  }}
                  style={styles.linkButton}
                >
                  Register
                </button>
              </div>

            </form>
          ) : (
            <form onSubmit={register} style={styles.form}>

              <input
                type="text"
                value={registerUsername}
                onChange={(e) => setRegisterUsername(e.target.value)}
                placeholder="Username"
                style={styles.input}
                required
              />

              <input
                type="email"
                value={registerEmail}
                onChange={(e) => setRegisterEmail(e.target.value)}
                placeholder="Email"
                style={styles.input}
                required
              />

              <input
                type="password"
                value={registerPassword}
                onChange={(e) => setRegisterPassword(e.target.value)}
                placeholder="Password"
                style={styles.input}
                required
              />

              <button
                type="submit"
                style={styles.primaryButton}
              >
                Register
              </button>

              <div style={styles.switchRow}>
                <span style={styles.switchText}>Already have an account?</span>
                <button
                  type="button"
                  onClick={() => {
                    setAuthMode("login");
                    setMessage("");
                  }}
                  style={styles.linkButton}
                >
                  Login
                </button>
              </div>

            </form>
          )}

        </div>

      </div>
    );
  }

  // ==========================================
  // PRODUCT MANAGEMENT PAGE
  // ==========================================

  return (
    <div style={styles.page}>

      <div style={styles.container}>

        <div style={styles.header}>

          <h1 style={styles.pageTitle}>
            Product Management
          </h1>

          <div style={styles.userBar}>
            <span style={styles.userText}>
              Welcome, {user.username}
            </span>

            <button
              onClick={logout}
              style={styles.logoutButton}
            >
              Logout
            </button>
          </div>

        </div>

        <div style={styles.banner}>
          Welcome, {user.username}!
        </div>

        {/* =====================================
            MESSAGE
        ====================================== */}

        {message && (
          <div style={styles.message}>
            {message}
          </div>
        )}

        {/* =====================================
            PRODUCTS HEADER
        ====================================== */}

        <div style={styles.productHeader}>

          <h2 style={styles.sectionTitle}>
            Products
          </h2>

          <div style={styles.buttonGroup}>

            <button
              onClick={() => loadProducts()}
              style={styles.refreshButton}
            >
              Refresh
            </button>

            <button
              onClick={openAddForm}
              style={styles.addButton}
            >
              + Add Product
            </button>

          </div>

        </div>

        {/* =====================================
            ADD / EDIT FORM
        ====================================== */}

        {showForm && (
          <div style={styles.formCard}>

            <h2 style={styles.formTitle}>
              {editingId !== null
                ? "Edit Product"
                : "Add Product"}
            </h2>

            <form onSubmit={saveProduct}>

              <label style={styles.label}>
                Product Name
              </label>

              <input
                type="text"
                value={productName}
                onChange={(e) =>
                  setProductName(e.target.value)
                }
                placeholder="Enter product name"
                style={styles.input}
                required
              />

              <label style={styles.label}>
                Description
              </label>

              <textarea
                value={description}
                onChange={(e) =>
                  setDescription(e.target.value)
                }
                placeholder="Enter description"
                style={styles.textarea}
                required
              />

              <label style={styles.label}>
                Price
              </label>

              <input
                type="number"
                step="0.01"
                min="0"
                value={price}
                onChange={(e) =>
                  setPrice(e.target.value)
                }
                placeholder="Enter price"
                style={styles.input}
                required
              />

              <label style={styles.label}>
                Quantity
              </label>

              <input
                type="number"
                min="0"
                value={quantity}
                onChange={(e) =>
                  setQuantity(e.target.value)
                }
                placeholder="Enter quantity"
                style={styles.input}
                required
              />

              <div style={styles.formButtons}>

                <button
                  type="submit"
                  style={styles.saveButton}
                >
                  {editingId !== null
                    ? "Update Product"
                    : "Add Product"}
                </button>

                <button
                  type="button"
                  onClick={closeForm}
                  style={styles.cancelButton}
                >
                  Cancel
                </button>

              </div>

            </form>

          </div>
        )}

        {/* =====================================
            PRODUCTS TABLE
        ====================================== */}

        <div style={styles.tableCard}>

          {loading ? (

            <p style={styles.loading}>
              Loading products...
            </p>

          ) : products.length === 0 ? (

            <p style={styles.empty}>
              No products found.
            </p>

          ) : (

            <div style={styles.tableWrapper}>

              <table style={styles.table}>

                <thead>

                  <tr>

                    <th style={styles.th}>
                      ID
                    </th>

                    <th style={styles.th}>
                      Product Name
                    </th>

                    <th style={styles.th}>
                      Description
                    </th>

                    <th style={styles.th}>
                      Price
                    </th>

                    <th style={styles.th}>
                      Quantity
                    </th>

                    <th style={styles.th}>
                      Created
                    </th>

                    <th style={styles.th}>
                      Actions
                    </th>

                  </tr>

                </thead>

                <tbody>

                  {products.map((product) => (

                    <tr key={product.id}>

                      <td style={styles.td}>
                        {product.id}
                      </td>

                      <td style={styles.td}>
                        {product.product_name}
                      </td>

                      <td style={styles.td}>
                        {product.description}
                      </td>

                      <td style={styles.td}>
                        ₱
                        {Number(
                          product.price
                        ).toLocaleString(
                          "en-PH",
                          {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                          }
                        )}
                      </td>

                      <td style={styles.td}>
                        {product.quantity}
                      </td>

                      <td style={styles.td}>
                        {product.created_at}
                      </td>

                      <td style={styles.td}>

                          <div
                            style={
                              styles.actionButtons
                            }
                          >

                            <button
                              onClick={() =>
                                openEditForm(product)
                              }
                              style={
                                styles.editButton
                              }
                            >
                              Edit
                            </button>

                            <button
                              onClick={() =>
                                deleteProduct(
                                  product.id
                                )
                              }
                              style={
                                styles.deleteButton
                              }
                            >
                              Delete
                            </button>

                          </div>

                      </td>

                    </tr>

                  ))}

                </tbody>

              </table>

            </div>

          )}

        </div>

      </div>

    </div>
  );
}

// ==========================================
// STYLES
// ==========================================

const styles = {

  page: {
    minHeight: "100vh",
    background: "#111",
    padding: "2rem",
    fontFamily:
      "Arial, Helvetica, sans-serif",
    boxSizing: "border-box",
    color: "#f5f5f5",
  },

  loginPage: {
    minHeight: "100vh",
    display: "grid",
    placeItems: "center",
    background: "#111",
    color: "#f5f5f5",
    fontFamily: "Arial, sans-serif",
    padding: "20px",
    boxSizing: "border-box",
  },

  container: {
    maxWidth: "1100px",
    margin: "0 auto",
    boxSizing: "border-box",
  },

  // ========================================
  // LOGIN
  // ========================================

  loginCard: {
    background: "#1a1a1a",
    border: "1px solid #2d2d2d",
    borderRadius: "12px",
    padding: "2rem",
    width: "min(420px, 90vw)",
    boxSizing: "border-box",
    display: "grid",
    gap: "1rem",
  },

  loginTitle: {
    margin: 0,
    fontSize: "3.5rem",
    lineHeight: 1.1,
    color: "#f5f5f5",
    fontWeight: 700,
  },

  form: {
    display: "grid",
    gap: "1rem",
  },

  label: {
    display: "block",
    marginBottom: "7px",
    marginTop: "15px",
    fontWeight: "600",
    color: "#374151",
  },

  input: {
    width: "100%",
    padding: "0.8rem",
    borderRadius: "8px",
    border: "1px solid #333",
    background: "#111",
    color: "#ffffff",
    boxSizing: "border-box",
    fontSize: "1rem",
    outline: "none",
  },

  textarea: {
    width: "100%",
    minHeight: "100px",
    padding: "11px 12px",
    border: "1px solid #d1d5db",
    borderRadius: "7px",
    fontSize: "15px",
    boxSizing: "border-box",
    resize: "vertical",
    fontFamily:
      "Arial, Helvetica, sans-serif",
  },

  primaryButton: {
    width: "100%",
    background: "#dd4814",
    color: "#ffffff",
    border: "none",
    padding: "0.9rem",
    borderRadius: "8px",
    cursor: "pointer",
    fontSize: "1.1rem",
    fontWeight: 700,
    transition: "opacity 0.2s ease",
  },

  switchRow: {
    display: "flex",
    alignItems: "center",
    gap: "0.35rem",
    marginTop: "0.25rem",
    fontSize: "1.05rem",
    color: "#f5f5f5",
    flexWrap: "wrap",
  },

  switchText: {
    color: "#f5f5f5",
    fontSize: "1.1rem",
  },

  linkButton: {
    background: "transparent",
    border: "none",
    color: "#dd4814",
    padding: 0,
    fontSize: "1.05rem",
    fontWeight: 700,
    cursor: "pointer",
  },

  flashError: {
    background: "#fee2e2",
    color: "#991b1b",
    border: "1px solid #fca5a5",
    borderRadius: "8px",
    padding: "0.8rem 1rem",
    marginBottom: "1rem",
    fontWeight: 600,
    display: "flex",
    alignItems: "center",
    gap: "10px",
  },

  flashSuccess: {
    background: "#dcfce7",
    color: "#166534",
    border: "1px solid #86efac",
    borderRadius: "8px",
    padding: "0.8rem 1rem",
    marginBottom: "1rem",
    fontWeight: 600,
    display: "flex",
    alignItems: "center",
    gap: "10px",
  },

  errorIcon: {
    width: "22px",
    height: "22px",
    borderRadius: "50%",
    background: "#991b1b",
    color: "#ffffff",
    fontSize: "0.8rem",
    display: "inline-flex",
    alignItems: "center",
    justifyContent: "center",
    flexShrink: 0,
  },

  successIcon: {
    width: "22px",
    height: "22px",
    borderRadius: "50%",
    background: "#15803d",
    color: "#ffffff",
    fontSize: "0.8rem",
    display: "inline-flex",
    alignItems: "center",
    justifyContent: "center",
    flexShrink: 0,
  },

  // ========================================
  // LOGIN SUCCESS NOTIFICATION
  // ========================================

loginSuccess: {
  position: "fixed",
  top: "20px",
  left: "50%",
  transform: "translateX(-50%)",
  backgroundColor: "#16a34a",
  color: "#ffffff",
  padding: "15px 30px",
  borderRadius: "10px",
  boxShadow: "0 6px 20px rgba(0, 0, 0, 0.2)",
  zIndex: 9999,
  minWidth: "300px",
  textAlign: "center",
  fontSize: "15px",
  lineHeight: "1.5",
},

  successTitle: {
    fontWeight: "bold",
    fontSize: "16px",
    marginBottom: "4px",
  },

  // ========================================
  // HEADER
  // ========================================

  header: {
    display: "flex",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: "1.5rem",
    gap: "1rem",
  },

  pageTitle: {
    margin: 0,
    fontSize: "2.2rem",
    lineHeight: 1.2,
    color: "#ffffff",
    fontWeight: 700,
  },

  userBar: {
    display: "flex",
    alignItems: "center",
    gap: "0.75rem",
    flexShrink: 0,
  },

  userText: {
    color: "#c9c9c9",
    fontSize: "1rem",
    lineHeight: 1.2,
  },

  logoutButton: {
    display: "inline-block",
    padding: "0.7rem 1rem",
    borderRadius: "8px",
    textDecoration: "none",
    color: "#ffffff",
    background: "#dd4814",
    border: "none",
    fontWeight: 600,
    cursor: "pointer",
  },

  banner: {
    background: "#164e2b",
    color: "#b7ffd0",
    padding: "14px 18px",
    border: "1px solid #22c55e",
    borderRadius: "8px",
    marginBottom: "20px",
    textAlign: "center",
    fontWeight: 600,
  },

  // ========================================
  // MESSAGE
  // ========================================

  message: {
    background: "#164e2b",
    color: "#b7ffd0",
    padding: "14px 18px",
    border: "1px solid #22c55e",
    borderRadius: "8px",
    marginBottom: "20px",
    textAlign: "center",
    fontWeight: 600,
  },

  // ========================================
  // PRODUCT HEADER
  // ========================================

  productHeader: {
    display: "flex",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: "20px",
    gap: "15px",
    flexWrap: "wrap",
  },

  sectionTitle: {
    margin: 0,
    color: "#f5f5f5",
    fontSize: "22px",
  },

  buttonGroup: {
    display: "flex",
    gap: "10px",
    flexWrap: "wrap",
  },

  refreshButton: {
    display: "inline-block",
    padding: "0.7rem 1rem",
    borderRadius: "8px",
    textDecoration: "none",
    color: "#ffffff",
    background: "#dd4814",
    border: "none",
    fontWeight: 600,
    cursor: "pointer",
  },

  addButton: {
    display: "inline-block",
    padding: "0.7rem 1rem",
    borderRadius: "8px",
    textDecoration: "none",
    color: "#ffffff",
    background: "#dd4814",
    border: "none",
    fontWeight: 600,
    cursor: "pointer",
  },

  // ========================================
  // FORM
  // ========================================

  formCard: {
    backgroundColor: "#ffffff",
    padding: "25px",
    borderRadius: "12px",
    boxShadow:
      "0 2px 10px rgba(0, 0, 0, 0.05)",
    marginBottom: "20px",
  },

  formTitle: {
    marginTop: 0,
    color: "#1f2937",
  },

  formButtons: {
    display: "flex",
    gap: "10px",
    marginTop: "25px",
  },

  saveButton: {
    padding: "11px 18px",
    border: "none",
    borderRadius: "7px",
    backgroundColor: "#16a34a",
    color: "#ffffff",
    fontWeight: "600",
    cursor: "pointer",
  },

  cancelButton: {
    padding: "11px 18px",
    border: "none",
    borderRadius: "7px",
    backgroundColor: "#6b7280",
    color: "#ffffff",
    fontWeight: "600",
    cursor: "pointer",
  },

  // ========================================
  // TABLE
  // ========================================

  tableCard: {
    background: "#181818",
    border: "1px solid #2a2a2a",
    borderRadius: "10px",
    overflow: "hidden",
  },

  tableWrapper: {
    width: "100%",
    overflowX: "auto",
  },

  table: {
    width: "100%",
    borderCollapse: "collapse",
    minWidth: "850px",
  },

  th: {
    textAlign: "left",
    padding: "0.9rem 1rem",
    backgroundColor: "#1f1f1f",
    color: "#f5f5f5",
    fontSize: "14px",
    borderBottom: "1px solid #2a2a2a",
  },

  td: {
    padding: "0.9rem 1rem",
    borderBottom: "1px solid #2a2a2a",
    color: "#d8d8d8",
    fontSize: "14px",
    verticalAlign: "middle",
  },

  // ========================================
  // ACTION BUTTONS
  // ========================================

  actionButtons: {
    display: "flex",
    gap: "7px",
  },

  editButton: {
    padding: "7px 12px",
    border: "none",
    borderRadius: "5px",
    backgroundColor: "#f59e0b",
    color: "#ffffff",
    fontWeight: "600",
    cursor: "pointer",
  },

  deleteButton: {
    padding: "7px 12px",
    border: "none",
    borderRadius: "5px",
    backgroundColor: "#dc2626",
    color: "#ffffff",
    fontWeight: "600",
    cursor: "pointer",
  },

  // ========================================
  // LOADING / EMPTY
  // ========================================

  loading: {
    padding: "30px",
    textAlign: "center",
    color: "#6b7280",
  },

  empty: {
    padding: "2rem",
    textAlign: "center",
    color: "#d8d8d8",
    background: "#181818",
    border: "1px solid #2a2a2a",
  },
};

export default App;