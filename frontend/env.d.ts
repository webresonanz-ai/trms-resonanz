/// <reference types="vite/client" />

interface DetectedBarcode {
  rawValue?: string;
}

interface BarcodeDetector {
  detect(source: CanvasImageSource): Promise<DetectedBarcode[]>;
}

interface BarcodeDetectorOptions {
  formats?: string[];
}

interface BarcodeDetectorConstructor {
  new (options?: BarcodeDetectorOptions): BarcodeDetector;
}

interface Window {
  BarcodeDetector?: BarcodeDetectorConstructor;
}
