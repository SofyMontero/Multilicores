import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import App from './App.jsx'
import { CartProvider } from './context/CartContext.jsx'
import { ClienteProvider } from './context/ClienteContext.jsx'
import './index.css'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <BrowserRouter basename="/sistema/app">
      <ClienteProvider>
        <CartProvider>
          <App />
        </CartProvider>
      </ClienteProvider>
    </BrowserRouter>
  </StrictMode>,
)
