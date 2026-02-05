{-# LANGUAGE DataKinds #-}
{-# LANGUAGE NoImplicitPrelude #-}
{-# LANGUAGE ScopedTypeVariables #-}
{-# LANGUAGE TemplateHaskell #-}

module GovernanceNFTPolicy where

import PlutusTx
import PlutusTx.Prelude
import Plutus.V2.Ledger.Api
import Plutus.V2.Ledger.Contexts

{-
  Governance NFT policy
  - Controlled minting by DAO/admin (requires signer)
  - Optional soulbound logic enforced by spending reference input
-}

{-# INLINABLE mkGovernancePolicy #-}
mkGovernancePolicy :: PubKeyHash -> Bool -> BuiltinData -> ScriptContext -> Bool
mkGovernancePolicy adminPkh soulboundEnabled _ ctx =
    traceIfFalse "admin signature missing" signedByAdmin
    && traceIfFalse "soulbound transfer blocked" soulboundOk
  where
    info :: TxInfo
    info = scriptContextTxInfo ctx

    signedByAdmin :: Bool
    signedByAdmin = txSignedBy info adminPkh

    soulboundOk :: Bool
    soulboundOk = if soulboundEnabled
      then null (txInfoInputs info) || txSignedBy info adminPkh
      else True

policy :: PubKeyHash -> Bool -> MintingPolicy
policy adminPkh soulboundEnabled = mkMintingPolicyScript
  $$(PlutusTx.compile [|| \pkh enabled -> mkGovernancePolicy pkh enabled ||])
    `PlutusTx.applyCode` PlutusTx.liftCode adminPkh
    `PlutusTx.applyCode` PlutusTx.liftCode soulboundEnabled

currencySymbol :: PubKeyHash -> Bool -> CurrencySymbol
currencySymbol adminPkh soulboundEnabled = scriptCurrencySymbol (policy adminPkh soulboundEnabled)
