import { useNavigate } from 'react-router-dom'
import { formatMoney } from '../api/client.js'
import { useCart } from '../context/CartContext.jsx'
import { useCliente } from '../context/ClienteContext.jsx'
import { withCli } from './Header.jsx'

export default function CartDrawer() {
  const {
    items,
    total,
    cartOpen,
    setCartOpen,
    updateQty,
    removeItem,
    observaciones,
    setObservaciones,
  } = useCart()
  const { telefono } = useCliente()
  const navigate = useNavigate()

  if (!cartOpen) return null

  return (
    <div className="drawer-overlay" onClick={() => setCartOpen(false)}>
      <aside
        className="drawer"
        onClick={(e) => e.stopPropagation()}
        aria-label="Carrito"
      >
        <div className="drawer__head">
          <h2>Tu pedido</h2>
          <button type="button" className="icon-btn" onClick={() => setCartOpen(false)}>
            ✕
          </button>
        </div>

        {items.length === 0 ? (
          <p className="muted drawer__empty">El carrito está vacío</p>
        ) : (
          <ul className="cart-list">
            {items.map((item, index) => (
              <li key={`${item.id}-${item.tipo}-${index}`} className="cart-item">
                <div>
                  <strong>{item.nombre}</strong>
                  <div className="muted small">
                    {item.tipo} · {formatMoney(item.precioUnitario)}
                  </div>
                </div>
                <div className="cart-item__actions">
                  <input
                    type="number"
                    min="1"
                    value={item.cantidad}
                    onChange={(e) => updateQty(index, e.target.value)}
                    aria-label="Cantidad"
                  />
                  <span>{formatMoney(item.precioTotal)}</span>
                  <button type="button" className="linkish" onClick={() => removeItem(index)}>
                    Quitar
                  </button>
                </div>
              </li>
            ))}
          </ul>
        )}

        <label className="field">
          <span>Observaciones</span>
          <textarea
            rows={3}
            value={observaciones}
            onChange={(e) => setObservaciones(e.target.value)}
            placeholder="Ej: dejar en portería"
          />
        </label>

        <div className="drawer__foot">
          <div className="drawer__total">
            <span>Total</span>
            <strong>{formatMoney(total)}</strong>
          </div>
          <button
            type="button"
            className="btn btn-primary"
            disabled={items.length === 0}
            onClick={() => {
              setCartOpen(false)
              navigate(withCli('/checkout', telefono))
            }}
          >
            Continuar pedido
          </button>
        </div>
      </aside>
    </div>
  )
}
